<?php

class GithubPluginUpdater
{
    private $slug;
    private $plugin;
    private $username;
    private $repository;
    private $path;
    private $data;
    private $token;
    private $isActivated;

    public function __construct($path, $username, $repository, $token = "")
    {
        add_filter("pre_set_site_transient_update_plugins", array( $this, "setTransitent" ));
        add_filter("plugins_api", array( $this, "setPluginInfo" ), 10, 3);
        add_filter("upgrader_pre_install", array( $this, "preInstall" ), 10, 3);
        add_filter("upgrader_post_install", array( $this, "postInstall" ), 10, 3);

        $this->path = $path;
        $this->username = $username;
        $this->repository = $repository;
        $this->token = $token;
    }

    private function init()
    {
        $this->slug = plugin_basename($this->path);
        $this->plugin = get_plugin_data($this->path);
    }

    public function getRepositoryInfo()
    {
        if (! empty($this->data)) {
            return;
        }

        $url = "https://api.github.com/repos/{$this->username}/{$this->repository}/releases";

        if (! empty($this->token)) {
            $url = add_query_arg(array( "access_token" => $this->token ), $url);
        }

        $this->data = wp_remote_retrieve_body(wp_remote_get($url));

        if (! empty($this->data)) {
            $this->data = @json_decode($this->data);
        }

        if (is_array($this->data)) {
            $this->data = $this->data[0];
        }

        if (! empty($this->data->message)) {
            WebProfit::error($this->data->message);
        }
    }

    public function setTransitent($transient)
    {
        if (empty($transient->checked)) {
            return $transient;
        }

        $this->init();
        $this->getRepositoryInfo();

        if (!empty($this->data->tag_name) && !empty($transient->checked[$this->slug])) {
            $doUpdate = version_compare($this->data->tag_name, $transient->checked[$this->slug]);
        } else {
            $transient->locale = null;
            $transient->response = null;
            $doUpdate = false;
            return $transient;
        }

        if ($doUpdate) {
            $package = $this->data->zipball_url;

            if (! empty($this->token)) {
                $package = add_query_arg(array( "access_token" => $this->token ), $package);
            }

            $object = new stdClass();
            $object->slug = $this->slug;
            $object->new_version = $this->data->tag_name;
            $object->url = $this->plugin["PluginURI"];
            $object->package = $package;

            $transient->response[$this->slug] = $object;
        }

        return $transient;
    }

    public function setPluginInfo($false, $action, $response)
    {
        $this->init();
        $this->getRepositoryInfo();

        if (empty($response->slug) || $response->slug != $this->slug) {
            return $false;
        }

        $response->last_updated = $this->data->published_at;
        $response->slug = $this->slug;
        $response->name = $this->plugin["Name"];
        $response->banners = array(
            "low" => WEBPROFIT_PLUGIN_URL . "/banner.jpg",
            "high" => WEBPROFIT_PLUGIN_URL . "/banner.jpg"
        );
        $response->plugin_name  = $this->plugin["Name"];
        $response->version = $this->data->tag_name;
        $response->author = $this->plugin["AuthorName"];
        $response->homepage = $this->plugin["PluginURI"];

        $download = $this->data->zipball_url;

        if (!empty($this->token)) {
            $download = add_query_arg(
                array( "access_token" => $this->token ),
                $download
            );
        }

        $response->download_link = $download;

        $response->sections = array(
            'Description' => $this->plugin["Description"],
            'Changelog' => $this->data->body
        );

        $matches = null;
        preg_match("/Requires at least:\s([\d\.]+)/i", $this->data->body, $matches);
        if (! empty($matches)) {
            if (is_array($matches)) {
                if (count($matches) > 1) {
                    $response->requires = $matches[1];
                }
            }
        }

        $matches = null;
        preg_match("/Tested up to:\s([\d\.]+)/i", $this->data->body, $matches);
        if (! empty($matches)) {
            if (is_array($matches)) {
                if (count($matches) > 1) {
                    $response->tested = $matches[1];
                }
            }
        }

        return $response;
    }

    public function preInstall($true, $args)
    {
        $this->init();
    }

    public function postInstall($true, $hook_extra, $result)
    {
        global $wp_filesystem;
        $pluginFolder = WP_PLUGIN_DIR . DIRECTORY_SEPARATOR . dirname($this->slug);
        $wp_filesystem->move($result['destination'], $pluginFolder);
        $result['destination'] = $pluginFolder;

        if ($this->isActivated) {
            $activate = activate_plugin($this->slug);
        }

        return $result;
    }
}

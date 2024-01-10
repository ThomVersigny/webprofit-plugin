<?php

class GitlabPluginUpdater
{
    private $slug = WEBPROFIT_SLUG;
    private $plugin;
    private $repository;
    private $path;
    private $data;
    private $token;
    private $isActivated;
    private $apiVersion = 'v4';

    public function __construct($path, $repository, $token = "")
    {
        add_filter("pre_set_site_transient_update_plugins", array( $this, "setTransitent" ));
        add_filter("plugins_api", array( $this, "setPluginInfo" ), 10, 3);
        add_filter("upgrader_pre_install", array( $this, "preInstall" ), 10, 3);
        add_filter("upgrader_post_install", array( $this, "postInstall" ), 10, 3);

        $this->path = $path;
        $this->repository = $repository;
        $this->token = $token;
    }

    private function init()
    {
        $this->slug = plugin_basename($this->path);
        $this->plugin = get_plugin_data($this->path);
    }

    private function getRepositoryInfo()
    {
        if (! empty($this->data)) {
            return;
        }

        $url = "https://gitlab.com/api/{$this->apiVersion}/projects/{$this->repository}/repository/tags";
        
        if (! empty($this->token)) {
            $url = add_query_arg(array( "private_token" => $this->token ), $url);
        }

        $response = wp_remote_get($url);
        $this->data = wp_remote_retrieve_body($response);

        if (wp_remote_retrieve_response_code($response) === 401) {
            return WebProfit::admin_error('Updater - ' . $this->data->message);
        }

        if (! empty($this->data)) {
            $this->data = @json_decode($this->data);
        }

        if (is_array($this->data)) {
            $this->data = $this->data[0];
        }
    }

    public function setTransitent($transient)
    {
        if (empty($transient->checked)) {
            return $transient;
        }

        $this->init();
        $this->getRepositoryInfo();
        
        if (!empty($this->data->name) && !empty($transient->checked[$this->slug])) {
            $doUpdate = version_compare($this->data->name, $transient->checked[$this->slug]);
        } else {
            $transient->locale = null;
            $transient->response = null;
            $doUpdate = false;
            return $transient;
        }

        if ($doUpdate) {
            $package = "https://gitlab.com/api/{$this->apiVersion}/projects/{$this->repository}/repository/archive.zip?sha={$this->data->name}";
            
            if (! empty($this->token)) {
                $package = add_query_arg(array( "private_token" => $this->token ), $package);
            }

            $object = new stdClass();
            $object->slug = $this->slug;
            $object->new_version = $this->data->name;
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
        $commit = $this->data->commit;
        $response->last_updated = $commit->created_at;
        $response->slug = $this->slug;
        $response->name = $this->plugin["Name"];
        // $response->banners = array(
        // 	"low" => WEBPROFIT_PLUGIN_URL . "/banner.jpg",
        // 	"high" => WEBPROFIT_PLUGIN_URL . "/banner.jpg"
        // );
        $response->plugin_name  = $this->plugin["Name"];
        $response->version = $this->data->name;
        $response->author = $this->plugin["AuthorName"];
        $response->homepage = $this->plugin["PluginURI"];

        $download = "https://gitlab.com/api/{$this->apiVersion}/projects/{$this->repository}/repository/archive.zip?sha={$this->data->name}";

        if (!empty($this->token)) {
            $download = add_query_arg(
                array( "private_token" => $this->token ),
                $download
            );
        }

        $response->download_link = $download;

        $response->sections = array(
            'Description' => $this->plugin["Description"],
            'Changelog' => $this->data->message
        );

        $matches = null;
        preg_match("/Requires at least:\s([\d\.]+)/i", $this->data->message, $matches);
        if (! empty($matches)) {
            if (is_array($matches)) {
                if (count($matches) > 1) {
                    $response->requires = $matches[1];
                }
            }
        }

        $matches = null;
        preg_match("/Tested up to:\s([\d\.]+)/i", $this->data->message, $matches);
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

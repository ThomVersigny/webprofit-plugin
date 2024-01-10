@echo off
git pull origin master

set /p commit=Whats changed? 
git add .
git commit -m "%commit%"

git push origin master
pause
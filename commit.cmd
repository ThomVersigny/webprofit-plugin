@echo off
set /p commit=Whats changed? 
git add .
git commit -m "%commit%"
pause
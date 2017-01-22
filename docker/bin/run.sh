#!/bin/bash

# Set timezone
ln -sf /usr/share/zoneinfo/Asia/Chongqing /etc/localtime

# Copy config file
cp $HEN_INSTALL_DIR/config/common.example.yml $HEN_INSTALL_DIR/config/common.yml

# Start cron service
service cron start

# Starts Android emulator in headless mode
emulator -avd android-$AVD_VERSION -no-window -no-boot-anim &
adb wait-for-device

# Starts Appium
appium


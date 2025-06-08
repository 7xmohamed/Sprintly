#!/bin/bash

# Stop any existing Waydroid processes
sudo waydroid session stop

# Start a new Waydroid session
sudo waydroid session start

# Launch Weston with WAYLAND_DISPLAY set to waydroid
weston --width=800 --height=600 &
WESTON_PID=$!

# Wait for Weston to start
sleep 2

# Set WAYLAND_DISPLAY to the Weston socket
export WAYLAND_DISPLAY=wayland-0

# Launch Waydroid in Weston
waydroid show-full-ui

# When Waydroid is closed, kill Weston
kill $WESTON_PID 
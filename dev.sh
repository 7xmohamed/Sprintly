#!/bin/bash

# Start all processes in the background
php artisan serve --no-ansi 2>&1 | sed -r 's/<[^>]*>//g' > /tmp/server.log &
SERVER_PID=$!

php artisan queue:listen --tries=1 --no-ansi 2>&1 | sed -r 's/<[^>]*>//g' > /tmp/queue.log &
QUEUE_PID=$!

php artisan pail --timeout=0 --no-ansi 2>&1 | sed -r 's/<[^>]*>//g' > /tmp/pail.log &
PAIL_PID=$!

npm run dev &
NPM_PID=$!

# Function to kill all processes
cleanup() {
    echo "Stopping all processes..."
    kill $SERVER_PID $QUEUE_PID $PAIL_PID $NPM_PID 2>/dev/null
    exit 0
}

# Trap Ctrl+C to properly clean up
trap cleanup INT

# Display logs in a cleaner format
echo "All services started. Press Ctrl+C to stop."
echo "Server running at http://127.0.0.1:8000"

# Keep the script running and tail logs
tail -f /tmp/server.log /tmp/queue.log /tmp/pail.log &
TAIL_PID=$!

# Wait for any of the processes to finish
wait $SERVER_PID $QUEUE_PID $PAIL_PID $NPM_PID
cleanup 
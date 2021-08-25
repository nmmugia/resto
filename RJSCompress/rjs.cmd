@echo off

echo Starting RJS
start /MIN "Node.js" node r.js -o build-admin.js
start /MIN "Node.js" node r.js -o build-bo-analytics.js
start /MIN "Node.js" node r.js -o build-bo-report.js
start /MIN "Node.js" node r.js -o build-cashier.js 
start /MIN "Node.js" node r.js -o build-checker.js 
start /MIN "Node.js" node r.js -o build-fast-order.js 
start /MIN "Node.js" node r.js -o build-hrd.js 
start /MIN "Node.js" node r.js -o build-kitchen.js 
start /MIN "Node.js" node r.js -o build-member.js 
start /MIN "Node.js" node r.js -o build-monitoring.js
start /MIN "Node.js" node r.js -o build-order.js
start /MIN "Node.js" node r.js -o build-reservation.js
start /MIN "Node.js" node r.js -o build-table.js 
exit
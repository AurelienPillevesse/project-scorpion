#!/bin/sh
echo "Building mediaz app"
#npm update
cd ./client/ScorpionClient/

echo "Building Client..."
#npm update
#bower update
gulp build

cd ../../

echo "Building Server"
cd ./server/ScorpionServer/
#composer update
cd ../../

echo "Building complete app"
gulp build
cp -r ./client/ScorpionClient/client/assets/img ./build/web/assets
echo "Done, use php app/console server:run to launch webserver"
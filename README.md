A Yii behavior to make things a little simpler for components that need to publish assets.
==========================================================================================

Just attach this behavior to a component that needs to publish assets and be sure to specify the "assetsDir" property. Then invoke getAssetsUrl() on your component or this behavior and the assets directory you specified earlier will be verified and published automatically for you.
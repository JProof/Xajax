@since 0.7.1

Modified PluginLayer

Typical Structure of an Plugin

    /Plugins
            /PluginName
                /Plugin.php     Holds the Plugin-Management                 // refactured   /plugin_layer/xajaxFunctionPlugin.inc.php
                /Handler.php    Configures and Prepares an Single-Request   // refactured   /plugin_layer/support/xajaxUserFunction.inc.php
                /Request.php    Request-Plugins has now there own inerhited Request class from  the old main xajaxRequest Class. This allows to configure or write more pluginfeatures


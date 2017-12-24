var scriptStackDir = '../../src/core/';
var files = [
    // scripts
    scriptStackDir + 'xajax.init.js',
    scriptStackDir + 'xajax.objects.js',
    //    scriptStackDir + 'xajax.config.js',
    //    scriptStackDir + 'xajax.config.cursor.js',
    scriptStackDir + 'xajax.tools.js',
    //   scriptStackDir + 'xajax.tools.queue.js',
    //   scriptStackDir + 'xajax.responseProcessor.js',
    //   scriptStackDir + 'xajax.responseProcessor.json.js',
    //  scriptStackDir + 'xajax.responseProcessor.xml.js',
    scriptStackDir + 'xajax.js.js',
    //   scriptStackDir + 'xajax.dom.js',
   // scriptStackDir + 'xajax.domResponse.js',
  
    scriptStackDir + 'xajax.css.js',
    scriptStackDir + 'xajax.forms.js',
    scriptStackDir + 'xajax.forms.values.js',
    scriptStackDir + 'xajax.events.js',
   // scriptStackDir + 'xajax.callback.js',
    // some extra tools
    scriptStackDir + 'xajax.attr.js',
 //   scriptStackDir + 'xajax.command.js',
    //"src/core/xajax.event.js"
//    scriptStackDir + 'xajax_core_old.js'
];

xajax={};
files.forEach(function (value) {
    require(value);
});
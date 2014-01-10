
    
function Apollo(config){
    this.scripts = [
        '/src/jquery.tmpl.min.js'
    ];
    this.cfg = {
        scriptServer: 'http://api.appros'
    };
    var self = this;
    this.loadjQuery(function(){
        self.setConfig(config);
        self.init();
    });
}
Apollo.prototype.setConfig = function(config){
    this.cfg = $.extend({
        //
    }, this.cfg, config);
}
Apollo.prototype.init = function(){
    
    var self = this;
    for (var s=0; s < self.scripts.length; s++) {
        this.include(self.scripts[s], function(){
            //
        });
    }
    
}
Apollo.prototype.loadjQuery = function(complete){
    if (typeof jQuery == 'undefined') {
        this.include('/src/jquery-1.10.2.min.js', complete);
    }
    else complete();
}
Apollo.prototype.include = function(src, complete){
    var el = document.createElement('script'); el.type = 'text/javascript'; el.async = true;
    el.src = this.cfg.scriptServer + src;
    el.onload = complete;
    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(el, s);
}

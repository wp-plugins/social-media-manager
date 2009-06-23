
jQuery.fn.twitterCounter = function(options) {
   var curSize = jQuery(this).val().length;
   var charsLeft = options['limit'] - curSize;
   var types = ['ok', 'watch', 'warning', 'error'];
   var x = {};
   jQuery.each(types,
   function() {
      var el = this.toString();
      x[el] = {
         'Max': options[el + 'Size'],
         'Style': options[el + 'Style'].substring(0, 1) == '.' || options[el + 'Style'].substring(0, 1) == '#' ? options[el + 'Style'].substring(1, options[el + 'Style'].length) : options[el + 'Style'],
         'Type': options[el + 'Style'].substring(0, 1) == '.' ? 'class': 'id'
      }
   });
   for (var i = 0; i < types.length; i++) {
      var el = types[i].toString(); // Last Element check
      if (i + 1 < types.length) {
         var nextEl = types[i + 1].toString();
         if (charsLeft > x[nextEl]['Max'] && charsLeft < x[el]['Max'] + 1) {
            clean();
         }
      } else {
         if (charsLeft < x[el]['Max']) {
            clean();
         }
      }
   }
   jQuery(options['counter']).text(charsLeft); // Add an event so the counter updates when the user types.
   jQuery(this).one('keyup',
   function() {
      jQuery(this).twitterCounter(options);
   });
   function clean() {
      if (x[el]['Type'] == 'class') {
         jQuery.each(types,
         function() {
            var temp = this.toString();
            if (jQuery(options['counter']).hasClass(temp)) {
               jQuery(options['counter']).removeClass(temp);
            }
         });
         jQuery(options['counter']).addClass(x[el]['Style']);
      } else {
         jQuery(options['counter']).id(x[el]['Style']);
      }
   }
};

jQuery(document).ready(function(){
    jQuery('#tstatus').twitterCounter({
        limit: 140,
        counter: '#textcounter',
        okSize: 140,
        okStyle: '.tok',
        watchSize: 20,
        watchStyle: '.twatch',
        warningSize: 10,
        warningStyle: '.twarning',
        errorSize: 0,
        errorStyle: '.terror'
    });
});


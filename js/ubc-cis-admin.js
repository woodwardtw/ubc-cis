console.log('foo gu')


//from https://stackoverflow.com/questions/17909646/counting-and-limiting-words-in-a-textarea
jQuery(document).ready(function() {
	let limit = parseInt(document.getElementById('short-desc-limit').innerHTML);//get limit number
    let usedDisplay = document.getElementById('short-desc-limit-used'); //place to show used
    let used = document.getElementById('acf-field_5ed26754e352d').value.match(/\S+/g).length; //count length on load
    usedDisplay.innerHTML = used;
  jQuery("#acf-field_5ed26754e352d").on('keyup', function() {
    var words = this.value.match(/\S+/g).length;
    
    if (words > limit) {
      // Split the string on first 200 words and rejoin on spaces
      var trimmed = jQuery(this).val().split(/\s+/, 200).join(" ");
      // Add a space at the end to make sure more typing creates new words
      jQuery(this).val(trimmed + " ");
    }
    else {
       console.log(words)
      usedDisplay.innerHTML = words;
      //jQuery('#word_left').text(limit-words);
    }
  });
}); 


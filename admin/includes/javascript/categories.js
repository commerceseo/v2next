
//for checking if at least one element is checked
function CheckMultiForm ()
  {
    var ml = document.multi_action_form;
    var len = ml.elements.length;
    for (var i = 0; i < len; i++) 
    {
      var e = ml.elements[i];
      if (e.name == "multi_products[]" || e.name == "multi_categories[]") 
      {
          if (e.checked == true) {
              return true;
          }
      }
    }
    alert('Bitte markieren Sie mindestens ein Element!\nPlease check at least one element!');
    return false;
  }

//for reverting checkboxes
function SwitchCheck ()
  {
    var maf = document.multi_action_form;
    var len = maf.length;
    for (var i = 0; i < len; i++) 
    {
      var e = maf.elements[i];
      if (e.name == "multi_products[]" || e.name == "multi_categories[]") 
      {
          if (e.checked == true) {
              e.checked = false;
          } else {
              e.checked = true;
          }
      }
    }
  }

//for checking all checkboxes
function CheckAll (wert)
  {
    var maf = document.multi_action_form;
    var len = maf.length;
    for (var i = 0; i < len; i++) 
    {
      var e = maf.elements[i];
      if (e.name == "multi_products[]" || e.name == "multi_categories[]") 
      {
        e.checked = wert;
      }
    }
  }
  

//for checking products only
function SwitchProducts ()
  {
    var maf = document.multi_action_form;
    var len = maf.length;
    var flag = false;
    for (var i = 0; i < len; i++) 
    {
      var e = maf.elements[i];
      if (e.name == "multi_products[]") 
      {
          if (flag == false) { 
              if (e.checked == true) { 
                  wert = false; 
              } else { 
                  wert = true; 
              } 
              flag = true; 
          }
          e.checked = wert;
      }
    }
  }

//for checking categories only
function SwitchCategories ()
  {
    var maf = document.multi_action_form;
    var len = maf.length;
    var flag = false;
    for (var i = 0; i < len; i++) 
    {
      var e = maf.elements[i];
      if (e.name == "multi_categories[]") 
      {
          if (flag == false) { 
              if (e.checked == true) { 
                  wert = false; 
              } else { 
                  wert = true; 
              } 
              flag = true; 
          }
          e.checked = wert;
      }
    }
  }

jQuery(document).ready(function() {
	jQuery(function() {
		jQuery("#cat_sortable").sortable({opacity: 0.6, cursor: 'move', update: function() {
			var order = jQuery(this).sortable("serialize") + '&action=updateCatSort';
			jQuery.post("includes/javascript/cat_sort.php", order, function(theResponse) {
				jQuery('#msg').html('<div id="success_msg" class="mysuccesslog">Die neue Sortierung wurde erfolgreich gespeichert.</div>');
			});
		}
		}).addClass('cat_move');
	});
});  

function showLayer(Name) {

        switch (Name) {
        case 'config':
        document.layers.l_export.visibility = "hide";
        document.layers.l_config.visibility = "show";
        document.layers.l_import.visibility = "show";
        break;

        case 'export':

        break;

        case 'import':

        break;
    }

}


function toggleBox(szDivID) {

  if (document.layers) { // NN4+
    if (document.layers[szDivID].visibility == 'visible') {
        document.layers[szDivID].visibility = "hide";
        document.layers[szDivID].display = "none";
        document.layers[szDivID+"SD"].fontWeight = "normal";
    } else {
        document.layers[szDivID].visibility = "show";
        document.layers[szDivID].display = "inline";
    }
  } else if (document.getElementById) { // gecko(NN6) + IE 5+
    var obj = document.getElementById(szDivID);
    if (obj.style.visibility == 'visible') {
        obj.style.visibility = "hidden";
        obj.style.display    = "none";
    } else {
        obj.style.visibility = "visible";
        obj.style.display    = "inline";
    }
  }
}

function SetFocus() {
  if (document.forms.length > 0) {
    var field = document.forms[0];
    for (i=0; i<field.length; i++) {
      if ( (field.elements[i].type != "image") && 
           (field.elements[i].type != "hidden") && 
           (field.elements[i].type != "reset") && 
           (field.elements[i].type != "submit") ) {

        document.forms[0].elements[i].focus();

        if ( (field.elements[i].type == "text") || 
             (field.elements[i].type == "password") )
          document.forms[0].elements[i].select();
        
        break;
      }
    }
  }
}
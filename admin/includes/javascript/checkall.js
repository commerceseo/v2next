
function check_all(name, el){ 
     if(!el || !el.form) return alert('falscher Parameter'); 
     var box = el.form.elements[name]; 
     if(!box) return alert(name + ' existiert nicht!'); 
     for(var i = 0; i < box.length; i++)  box[i].checked = el.checked; 
} 

 

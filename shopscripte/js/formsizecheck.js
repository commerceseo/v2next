function textcounter(field,count_field,max_limit)
{if(field.value.length>max_limit)
{field.value=field.value.substring(0,max_limit);}
else
{count_field.value=max_limit-field.value.length;}}
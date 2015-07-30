function checkAll(field)

{
  var field=$("input[id="+field+"]");

  for (i = 0; i < field.length; i++)

   field[i].checked = true ;

}




function uncheckAll(field)

{

   var field=$("input[id="+field+"]");

   for (i = 0; i < field.length; i++)

   field[i].checked = false ;

}



function massupdate(module_name)

{

	var thisForm = $('#list_view_form');  

	thisForm.attr('action',site_url+'/'+module_name+'/massupdate');

	thisForm.submit();

  //alert(a);

}

function massupdate_option(module_name,product_id)

{
   
	//alert(product_id);
	var thisForm = $('#list_view_form');  

	thisForm.attr('action',site_url+'/'+module_name+'/massupdate/'+product_id);

	thisForm.submit();

  //alert(a);

}


function massupdate_item(module_name)

{
   
	//alert(product_id);
	var thisForm = $('#update_item_list');  

	thisForm.attr('action',site_url+'/'+module_name+'/update_option_item');

	thisForm.submit();

  //alert(a);

}


function list_form_submit(module_name,action)

{

   $.ajax({ url: "<?php echo base_url(); ?>"+module_name+'/'+action, 

	          type: "POST",

			  data: "image_id="+image_id,

			  success: function(response){

			  $('#comments_section').html(response);

			  //load_next_pic(image_id);	

      }});	

}
function checkbox_check(f)
{
	var check = false; 
	var field=$("input[id="+f+"]");

  for (i = 0; i < field.length; i++){
	if(field[i].checked == true){
	  check = true;
	}
  }
  if(check == false)
  {
     if(f == 'delete_products')
	   alert("Please select atleast one product to delete");
	 if(f == 'category')
       alert("Please select atleast one category to delete");
  }
  return check;
}

function show_confirm()
{
   
var r=confirm("If you will delete this category then all the subcategories and their products also will be deleted.");
if (r==true)
  {
	alert("Are you sure you want to continue?");
	document.list_form.submit();
  }
else
  {
	return false;
  //alert("You pressed Cancel!");
  }
}

function delete_product_confirm(form)
{
   var r=confirm("Product(s) will be permanently deleted.");
	if (r==true)
    {
  	   alert("Are you sure you want to continue?");
	   if(form=='product_form')
	     document.product_form.submit();
       else	
	     //alert('pe gaya panga');
		 document.add_form.submit();
    }
	else
	{
	   return false;
	}
}

function delete_option_confirm()
{
   var r=confirm("Option will be permanently deleted.");
	if (r==true)
    {
  	   alert("Are you sure you want to continue?");
	   document.list_form.submit();
    }
	else
	{
	   return false;
	}
}

function delete_item_confirm()
{
   var r=confirm("Item will be permanently deleted.");
	if (r==true)
    {
  	   alert("Are you sure you want to continue?");
	   document.list_form.submit();
    }
	else
	{
	   return false;
	}
}

function delete_confirm(item)
{
   var r=confirm(item+" will be permanently deleted.");
	if (r==true)
    {
  	   alert("Are you sure you want to continue?");
    }
	else
	{
	   return false;
	}
}

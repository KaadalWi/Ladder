function openPopup(popupId)
{
   document.getElementById(popupId).style.display = "block";
}

function closePopup(popupId, clearForm)
{
   var popupForm;
   document.getElementById(popupId).style.display = "none";
   
   if (clearForm)
   {
      popupForm = document.getElementById(popupId.replace("_popup", ""));
      popupForm.reset();
      
      for (var i = 0; i < popupForm.elements.length; i++)
      {
         popupForm.elements[i].style.backgroundColor = "";
      }
   }
}
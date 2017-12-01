// Makes the given popup visible
function openPopup(popupId, nameToDisplay, username)
{
   document.getElementById("challengee_name").innerHTML = nameToDisplay;
   
   // Reset the popup's form
   popupForm = document.getElementById(popupId.replace("_popup", ""));
   popupForm.reset();
   document.getElementById("challengee_username").value = username;

   document.getElementById(popupId).style.display = "block";
}

// Hides the given popup, and clears it if prompted
function closePopup(popupId, nameFieldId)
{
   var popupForm;
   document.getElementById(popupId).style.display = "none";
}
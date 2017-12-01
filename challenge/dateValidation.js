function validateDay()
{
   var month = document.getElementById("match_month").value;
   var year = document.getElementById("match_year").value;
   var day_29 = document.getElementById("challenge_form_day_29");
   var day_30 = document.getElementById("challenge_form_day_30");
   var day_31 = document.getElementById("challenge_form_day_31");
   day_29.style.display = "";
   day_30.style.display = "";
   day_31.style.display = "";

   if (month == "04" || month == "06" || month == "09" || month == "11")
   {
      day_31.style.display = "none";
   }
   else if (month == "02")
   {
      if (year % 4 != 0)
      {
         day_29.style.display = "none";
      }
      day_30.style.display = "none";
      day_31.style.display = "none";
   }
}

function validateYear()
{
   var zeroAppend = "0";
   var years = document.getElementById("match_year");
   var currentTime = new Date();
   var currentYear;
   years.innerHTML = "";

   currentYear = currentTime.getFullYear();

   for (var i = 0; i < 25; i++)
   {
      years.innerHTML = years.innerHTML + '<option value="' +
         (currentYear + i) + '">&nbsp;' + (currentYear + i) +
         '</option>\n';
   }
}

function submitTime()
{
   var currentTime = new Date();
   var year = document.getElementById("match_year").value;
   var month = document.getElementById("match_month").value;
   var day = document.getElementById("match_day").value;
   var hour = document.getElementById("match_hour").value;
   var minute = document.getElementById("match_minute").value;
   var second = document.getElementById("match_second").value;
   var timeString = year + '-' + month + '-' + day + ' ' + hour + ':' + minute + ':' + second;
   document.getElementById("match_time").value = timeString;
}
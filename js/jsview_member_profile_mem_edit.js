// JavaScript Document

function validate()
{ 
   if(document.memberform.owner_name.value == "" )
   {
     //alert( "Please provide owner name!" );
	   document.getElementById("errorBox").innerHTML = "Please provide owner name!";
     document.memberform.owner_name.focus() ;
     document.getElementById('owner_name').style.background = "#ffff32";
     return false;
   }
   
   //var rgx = /^[.'+\w\s]*$/;
   var rgx = /^[.'-/\[/\]+\w\s]*$/;

   /*if(!document.memberform.owner_name.value.match(rgx))
   {
      document.getElementById("errorBox").innerHTML = "Please provide valid primary owner name!";
      document.memberform.primary_owner_name.focus() ;
      return false;  
   }*/
    

   /*if( document.memberform.resd_no.value == "" )
   {
     //alert( "Please provide residential no." );
	   document.getElementById("errorBox").innerHTML = "Please provide residential no!";
     document.memberform.resd_no.focus() ;
     return false;
   }*/
   
   
     /*if(document.memberform.mob.value == "")
     {
       //alert( "Please provide mobile no" );
  	 document.getElementById("errorBox").innerHTML = "Please provide mobile no!";
       document.memberform.mob.focus() ;
       return false;
     }
     
     if(document.memberform.email.value == "")
     {
       //alert( "Please provide email id" );
  	   document.getElementById("errorBox").innerHTML = "Please provide email id!";
       document.memberform.email.focus() ;
       return false;
     }*/

   var iOtherCnt = document.memberform.tot_other.value;
   var hasOwner = false; 
   var iOwnerCount = 0;

   if(iOtherCnt > 0)
   {
      for(var iCnt = 1 ; iCnt <= iOtherCnt ; iCnt++)
      {
        var fieldName = 'other_name' + iCnt;
        
        if(document.getElementById(fieldName).value == "")
        {
          document.getElementById("errorBox").innerHTML = "Please provide name of family <member></member>!";
          document.getElementById(fieldName).focus();
          document.getElementById(fieldName).style.background = "#ffff32";
          return false;
        }
        
        fieldName = 'coowner' + iCnt;
        if(document.getElementById(fieldName).value == 1)
        {
          iOwnerCount = iOwnerCount + 1;
          hasOwner = true;
        }
      }
   }

   if(hasOwner == false)
   {
      document.getElementById("errorBox").innerHTML = "Please select one Owner from the associated member list";
      document.getElementById('coowner1').focus();
      document.getElementById('coowner1').style.background = "#ffff32";
      return false;
   }

   if(iOtherCnt > 0)
   {
      for(var iCnt = 1 ; iCnt <= iOtherCnt ; iCnt++)
      {
        var fieldName = 'coowner' + iCnt;
        
        document.getElementById(fieldName).disabled = false;
      }
   }
   
   return( true );
}
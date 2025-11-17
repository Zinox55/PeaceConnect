submit=document.getElementById("submit1").addEventListener('click',function verify(event){
event.preventDefault();
user_email1=document.getElementById("email1");
user_email1.placeholder="Enter your email";
user_password1=document.getElementById("password1");
user_password1.placeholder="Enter your password";
submit=document.getElementById("submit1");
isValid=true;
 function displayMessage(id, message, isError) {
        var element = document.getElementById(id + "_error");
        element.style.color = isError ? "red" : "green";
        element.innerText = message;
    }




  emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
  if (emailRegex.test(user_email1) || user_email1.value.length==0) {
    displayMessage("user_email","you have to type your email adress. please try again.",true);
    isValid=false;
  }
if(user_password1.value.length===0 ){
    displayMessage("user_password","You have to chose a password",true);
    isValid=false;

}

});   


submit=document.getElementById("submit").addEventListener('click',function verify(event){
event.preventDefault();
username=document.getElementById("name");
username.placeholder="Enter your name";
user_email=document.getElementById("email");
user_email.placeholder="Enter your email";
user_password=document.getElementById("password");
user_password.placeholder="Enter your password";
user_password_v=document.getElementById("verify_password");
user_password_v.placeholder="Verify your password";

user_password1=document.getElementById("password1");
user_password1.placeholder="Enter your password";

submit=document.getElementById("submit");
isValid=true;
 function displayMessage(id, message, isError) {
        var element = document.getElementById(id + "_error");
        element.style.color = isError ? "red" : "green";
        element.innerText = message;
    }


if(username.value.length===0 ){
    displayMessage("username","U have to type a username",true);
    isValid=false;

}
emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
  if (emailRegex.test(user_email) || user_email.value.length==0) {
    displayMessage("user_email","you have to type your email adress. please try again.",true);
    isValid=false;
  }

if( user_password.value !== user_password_v.value){
     displayMessage("user_password_v","Wrong Password,Please try again",true);
    isValid=false;

}
if(user_Role.value===" "){
    displayMessage("user_Role","You have to chose a Role",true);
    isValid=false;

}
});   


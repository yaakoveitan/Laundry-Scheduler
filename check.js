
function checkFilled(id, password, first, last, apt, email, phone)
{
    return check(id, "User ID") && check(password, "Password") && check(first, "First Name") && check(last, "Last Name") && check(apt, "Apartment Number") && check(email, "Email") && check(phone, "Phone Number");
}

function check(element, field)
{
    let result = true;
    if(element.trim().length == 0)
    {
        alert(field + " must not be empty!");
        result = false;
    }
    
    return result;
}

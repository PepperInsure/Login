
$(document).ready(function()
{
    var inputs = 
    {
        username: "",
        password: "",
        button_pressed: 0
    };
    
    var indexname = ""; //id of input box
    
    $(".input-wrapper").keyup(function(event)
    {
        indexname = event.target.id;
        inputs[indexname] = $(event.target).val();

        //$('#test').append($(event.target).val());
    });
    
    $('#login').click(function()
    {
        $('#test').empty();
        inputs['button_pressed'] = 0;
        $.post("auth.php", {user_input: inputs}, function(data)
        {
            $('#test').empty();
            $('#test').append(data);
            

        });
    });
    
    $('#signup').click(function()
    {
        if(inputs.username == "" || inputs.password == "")
        {
            $('#test').empty();
            $('#test').append("Please fill in a username and password.");
        }
        else
        {
            inputs['button_pressed'] = 1;
            
            $.post("auth.php", {user_input: inputs}, function(data)
            {
                $('#test').empty();
                $('#test').append(data);
            });
        }
        //window.location.href = "https://second-login-pepperinsure.c9users.io/enter.html";
        //This works unlike the PHP one but seems insecure
    });
    
    $('#google').click(function()
    {
        
        inputs['button_pressed'] = 2;
        $.post("auth.php", {user_input: inputs}, function(data)
        {
            $('#test').empty();
            $('#test').append(data);
        });
        
        
    });
    
    
});

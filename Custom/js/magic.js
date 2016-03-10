// magic.js
$(document).ready(function() {
    alert("document is ready YES");
    
    // process the form
    $('form').submit(function(event) {

        alert("form submit YES");
        // Every time we submit the form, our errors from our previous submission are still there. We just need to clear them by removing them as soon as the form is submitted again.
        // $('.form-group').removeClass('has-error'); // remove the error class
        // $('.help-block').remove(); // remove the error text
        
        // get the form data
        // there are many ways to get this data using jQuery (you can use the class or id also)
        var formData = {
            'search_type'   : $('input[name=search_type]').val(),
            'book_title'    : $('input[name=book_title]').val(),
            'lname'         : $('input[name=lname]').val()
        };

        alert(formData.book_title);
            // <tr><td>Collection ID':</td>        <td><input type="text" size="30" name="collectionid"<?php if($collectionid) echo " value=\"$collectionid\""; ?>/> e.g. 4</td></tr>

            // <tr><td>Volume:</td>    <td><input type="text" size="30" name="volume"<?php if($volume) echo " value=\"$volume\""; ?>/> e.g. 2</td></tr>
            // <tr><td>Edition:</td>   <td><input type="text" size="30" name="edition"<?php if($edition) echo " value=\"$edition\""; ?>/> e.g. new</td></tr>
            // <tr><td>Year:</td>      <td><input type="text" size="30" name="year"<?php if($year) echo " value=\"$year\""; ?>/> e.g. 1825</td></tr>
            // <tr><td>Subject:</td>   <td><input type="text" size="30" name="subject"<?php if($subject) echo " value=\"$subject\""; ?>/></td></tr>
            // <tr><td>Language:</td>  <td><input type="text" size="30" name="language"<?php if($language) echo " value=\"$language\""; ?>/> e.g. eng



        // process the form
        $.ajax({
            type        : 'POST', // define the type of HTTP verb we want to use (POST for our form)
            url         : 'index.php', // the url where we want to POST
            data        : formData, // our data object
            dataType    : 'json', // what type of data do we expect back from the server
            encode      : true
        })
        
            // using the done promise callback
            .done(function(data) {

                alert("callback done");
                // log data to the console so we can see
                console.log(data); 

                /*
                // here we will handle errors and validation messages
                if ( ! data.success) {
                    // handle errors for name ---------------
                    if (data.errors.name) {
                        $('#name-group').addClass('has-error'); // add the error class to show red input
                        $('#name-group').append('<div class="help-block">' + data.errors.name + '</div>'); // add the actual error message under our input
                    }
                    // handle errors for email ---------------
                    if (data.errors.email) {
                        $('#email-group').addClass('has-error'); // add the error class to show red input
                        $('#email-group').append('<div class="help-block">' + data.errors.email + '</div>'); // add the actual error message under our input
                    }
                    // handle errors for superhero alias ---------------
                    if (data.errors.superheroAlias) {
                        $('#superhero-group').addClass('has-error'); // add the error class to show red input
                        $('#superhero-group').append('<div class="help-block">' + data.errors.superheroAlias + '</div>'); // add the actual error message under our input
                    }

                } else {

                    // ALL GOOD! just show the success message!
                    $('form').append('<div class="alert alert-success">' + data.message + '</div>');

                    // usually after form submission, you'll want to redirect
                    // window.location = '/thank-you'; // redirect a user to another page
                    alert('success'); // for now we'll just alert the user
                }
                */
                window.location = 'index.php';
            });
            
            /*
            // using the fail promise callback
            .fail(function(data) {

                // show any errors
                // best to remove for production
                console.log(data);
            });
            */
        
        // stop the form from submitting the normal way and refreshing the page
        event.preventDefault();
    });

});

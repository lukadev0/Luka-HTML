const validation = new JustValidate("#registrazione"); // Drop-in chiamato just_validate, qui vengono apportate le modifiche per la nostra applicazione

// Validazione client-side

validation
    .addField("#username", [
        {
            rule: "required",
            errorMessage: 'Username required.'
        },

        {
            validator: (value) => () => { // Questo validator controlla se uno username è già in utilizzo nel DB.
                return fetch("Username_esistente.php?username=" + encodeURIComponent(value))
                       .then(function(response) {
                           return response.json();
                       })
                       .then(function(json) {
                           return json.available;
                       });
            },
            errorMessage: "Username already used."
        }
    ])
    .addField("#password", [
        {
            rule: "required",
            errorMessage: 'Password required.'
        },

        {
            rule: "password",
            errorMessage: "Password have to be atleast 8 characters long."
        }
    ])
    .addField("#email", [
        {
            rule: "required",
            errorMessage: 'This field needs to be filled.' 
        },
        {
            rule: "email",
            errorMessage: 'Insert a valid email.'
        },
        {
            validator: (value) => () => { // Questo validator controlla se una email è già in utilizzo nel DB.
                return fetch("Email_esistente.php?email=" + encodeURIComponent(value))
                       .then(function(response) {
                           return response.json();
                       })
                       .then(function(json) {
                           return json.available;
                       });
            },
            errorMessage: "Email already used."
        }
    ])
    
    .onSuccess((event) => {
        document.getElementById("registrazione").submit();
    });
// alert("this is js");
(function ($) {
  "use strict";

  /**
   *  the Drupal.behaviors object is used to attach the form validation function to the form, and the once() method is used to ensure that the function is only attached once.
   */
  Drupal.behaviors.formValidation = {
    // Use context to filter the DOM to only the elements of interest,
    // and use once() to guarantee that our callback function processes
    // any given element one time at most, regardless of how many times
    // the behaviour itself is called (it is not sufficient in general
    // to assume an element will only ever appear in a single context).
    attach: function (context, settings) {
      var $form = $("form#user-details", context);

      // 'once'  Ensures a JavaScript callback is only executed once on a set of elements.
      /**
       * The once() method ensures that the validation is only attached once to the form, even if the form is loaded multiple times on the page
       */
      $form.once("custom_validation").on("submit", function (event) {
        var $name = $form.find('input[name="candidate_name"]');
        var $email = $form.find('input[name="candidate_mail"]');
        console.log($name);
        var name_regex = /[a-zA-Z]/;
        var email_regex = /^[\w-\.]+@([\w-]+\.)+[\w-]{2,4}$/;
        console.log(name_regex.test($name.val()));
        console.log(email_regex.test($email.val()), $email.val());

        if ($name.val() === "" || !name_regex.test($name.val())) {
          event.preventDefault();
          if ($name.val() === "") alert("name is required");
          else alert("enter valid name");
          $name.addClass("error");
          $form.find(".error-message").text("Name is required.");
          return false;
        }

        if ($email.val() === "" || !email_regex.test($email.val())) {
          event.preventDefault();
          if ($email.val() === "") alert("email is required");
          else alert("enter valid email");
          $email.addClass("error");
          $form.find(".error-message").text("email is required.");
          return false;
        }
      });
    },
  };
})(jQuery);

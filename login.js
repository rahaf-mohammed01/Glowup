// Wait for the DOM content to be fully loaded before executing the script
document.addEventListener('DOMContentLoaded', function() {
    // Get references to the 'signUp' button, 'signIn' button, and the container element
    const signUpButton = document.getElementById('signUp');
    const signInButton = document.getElementById('signIn');
    const container = document.getElementById('container');
  
    // Add a click event listener to the 'signUp' button
    signUpButton.addEventListener('click', function() {
      // Add the 'right-panel-active' class to the container, triggering the right panel to be active
      container.classList.add('right-panel-active');
    });
  
    // Add a click event listener to the 'signIn' button
    signInButton.addEventListener('click', function() {
      // Remove the 'right-panel-active' class from the container, deactivating the right panel
      container.classList.remove('right-panel-active');
    });
  });

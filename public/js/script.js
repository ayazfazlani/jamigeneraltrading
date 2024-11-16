const body = document.querySelector('body'),
      sidebar = body.querySelector('nav'),
      toggle = body.querySelector(".toggle"),
      searchBtn = body.querySelector(".search-box"),
      modeSwitch = body.querySelector(".toggle-switch"),
      modeText = body.querySelector(".mode-text");


toggle.addEventListener("click" , () =>{
    sidebar.classList.toggle("close");
})

searchBtn.addEventListener("click" , () =>{
    sidebar.classList.remove("close");
})

modeSwitch.addEventListener("click" , () =>{
    body.classList.toggle("dark");
    
    if(body.classList.contains("dark")){
        modeText.innerText = "Light mode";
    }else{
        modeText.innerText = "Dark mode";
        
    }
});









  // Toggle Notification Dropdown
  const notificationButton = document.getElementById('notificationButton');
  const notificationDropdown = document.getElementById('notificationDropdown');
  
  notificationButton.addEventListener('click', () => {
      notificationDropdown.classList.toggle('hidden');
  });

  // Toggle User Dropdown
  const userButton = document.getElementById('userButton');
  const userDropdown = document.getElementById('userDropdown');
  
  userButton.addEventListener('click', () => {
      userDropdown.classList.toggle('hidden');
  });

  // Close dropdowns if clicking outside
  window.addEventListener('click', (e) => {
      if (!notificationButton.contains(e.target) && !notificationDropdown.contains(e.target)) {
          notificationDropdown.classList.add('hidden');
      }
      if (!userButton.contains(e.target) && !userDropdown.contains(e.target)) {
          userDropdown.classList.add('hidden');
      }
  });

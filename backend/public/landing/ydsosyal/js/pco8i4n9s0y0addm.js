const sidebar = document.querySelector('.sidebar');
const sidebarContent = document.querySelector('.sidebar-content');

function toggleSidebar() {
  sidebar.classList.toggle('active');
  sidebarContent.classList.toggle('active');
}

function closeSidebar() {
  sidebar.classList.remove('active');
  sidebarContent.classList.remove('active');
}

function openModalID(id) {
  document.getElementById(id).classList.remove("disable")
}

function closeModalID(id) {
  document.getElementById(id).classList.add("disable")
}

function sidebarScrollBottom() {
  $('.sidebar-top').animate({
    scrollTop: $('.sidebar-top')[0].scrollHeight
  }, 1000);
}

function openMainHeader() {
  $('.main-header-mobile').addClass('active');
}

function closeMainHeader() {
  $('.main-header-mobile').removeClass('active');
}

function notifyOpen() {
  var duyuruBox = document.querySelector('.announcements-box');
  duyuruBox.classList.toggle('active');
}

function notifyClose() {
  var duyuruBox = document.querySelector('.announcements-box');
  duyuruBox.classList.remove('active');
}

function navigateTo(url) {
  window.location.href = url;
}
//import Waypoint from 'waypoint';
//Scrollpoints = require('scrollpoints');
import scrollpoints from 'scrollpoints';
import Vue from 'vue';

import Accordion from '../vue/Accordion.vue';
import Header from '../vue/Header.vue';
import JobSummary from '../vue/JobSummary.vue';
import PageContainer from '../vue/PageContainer.vue';
import Search from '../vue/Search.vue';
import TextBlock from '../vue/TextBlock.vue';
import VideoPlayer from '../vue/VideoPlayer.vue';


function toggleOpenNavMenu(navLink) {
    console.log('toggleOpenNavMenu');
    const openClassName = 'page-container--nav-menu-open';
    const pageContainer = navLink.closest('.page-container.js');
    if (pageContainer.classList.contains(openClassName)) {
        pageContainer.classList.remove(openClassName);
    } else {
        pageContainer.classList.add(openClassName);
    }
}

window.ppjNavTo = function(href, callback) {
  console.log('navTo');
  if (typeof callback !== 'undefined') {
    callback()
  }
  window.location = href;
};

window.addEventListener('load', function() {

  if (document.querySelectorAll('#site-container').length > 0) {

    Vue.component('accordion', Accordion);
    Vue.component('accordion-element', Accordion.childComponents.Element);
    Vue.component('job-summary', JobSummary);
    Vue.component('page-container', PageContainer);
    Vue.component('page-header', Header);
    Vue.component('search', Search);
    Vue.component('text-block', TextBlock);
    Vue.component('video-player', VideoPlayer);

    var vm = new Vue({
      el: '#site-container',
      methods: {
        pageLoaded: function () {
          this.$emit('pageLoaded');
        }
      }
    });

  }

  const scrollPointConfig = {
    when: 'entering'
  };

  // fade content in on scroll
  const els = document.querySelectorAll('.l-full, .l-half');
  for (let i in els) {
    scrollpoints.add(
      els[i],
      (el) => {
        el.style.opacity = 1
      },
      scrollPointConfig
    )
  }

  // hide html until Vue has rendered
  const html = document.getElementsByTagName('html')[0];
  html.style.opacity = 1;


}, false);

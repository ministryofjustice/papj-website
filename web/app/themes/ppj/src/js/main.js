//import Waypoint from 'waypoint';
//Scrollpoints = require('scrollpoints');
import scrollpoints from 'scrollpoints';
import Vue from 'vue';

import Accordion from '../vue/Accordion.vue';
import Contact from '../vue/Contact.vue';
import Header from '../vue/Header.vue';
import JobSummary from '../vue/JobSummary.vue';
import OrganizationSearch from '../vue/OrganizationSearch.vue';
import OrganizationSummary from '../vue/OrganizationSummary.vue';
import PageContainer from '../vue/PageContainer.vue';
import RoleIntro from '../vue/RoleIntro.vue';
import Search from '../vue/Search.vue';
import Tabs from '../vue/Tabs.vue';
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
    Vue.component('contact', Contact);
    Vue.component('job-summary', JobSummary);
    Vue.component('ol-element', OrderedList.childComponents.Element);
    Vue.component('ordered-list', OrderedList);
    Vue.component('organization-search', OrganizationSearch);
    Vue.component('organization-summary', OrganizationSummary);
    Vue.component('page-container', PageContainer);
    Vue.component('page-header', Header);
    Vue.component('role-intro', RoleIntro);
    Vue.component('search', Search);
    Vue.component('tabs', Tabs);
    Vue.component('tab', Tabs.childComponents.Tab);
    Vue.component('text-block', TextBlock);
    Vue.component('text-image-block', TextImageBlock);
    //Vue.component('video-player', VideoPlayer);

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


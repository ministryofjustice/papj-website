<template>

  <div class="search"
       v-cloak
       :class="{'search--job-selected': searchResults.selectedJobLocationGroupId}"
  >
    <div class="search__header">
      <h2 class="search__title">{{ titleText }}</h2>
      <p class="search__prompt">Enter location (postcode, town or region)</p>
      <div class="search__form">
        <input type="text"
               class="search__input"
               :placeholder="placeHolderText"
               editable="editable"
               v-model="searchResults.searchTerm"
               @click.stop.prevent=""
               @keypress="handleSearchInputKeyPress"/>
        <div class="search__button-clear-search-container">
          <button class="search__button-clear-search"
                  :class="{'search__button-clear-search--enabled': searchResults.searchTerm}"
                  @click.stop.prevent="handleClearSearchClick"
          >&#10005;</button>
        </div>

        <button class="search__button-search"
                @click.stop.prevent="search"
                :disabled="searchResults.searchTerm == ''">
          <div class="search__button-search-circle"></div>
          <div class="search__button-search-rectangle"></div>
        </button>
      </div>

      <div class="search__geolocation"
           :class="{
                'search__geolocation--is-busy': (geoLocationIsBusy == true),
                'search__geolocation--is-active': geoLocationIsActive
            }">
        <a class="search__geolocation-button"
           v-if="geoLocationIsAvailable"
           @click.stop.prevent="useGeoLocation">
          <svg version="1.1"
               id="Layer_1"
               xmlns="http://www.w3.org/2000/svg"
               xmlns:xlink="http://www.w3.org/1999/xlink"
               x="0px"
               y="0px"
               viewBox="0 0 20 20"
               xml:space="preserve"
               class="search__geolocation-icon"
          ><path class="st0" d="M17.5,9.3c-0.3-3.6-3.2-6.5-6.8-6.8V0H9.3v2.5C5.7,2.9,2.9,5.7,2.5,9.3H0v1.4h2.5c0.3,3.6,3.2,6.5,6.8,6.8V20
              h1.4v-2.5c3.6-0.3,6.5-3.2,6.8-6.8H20V9.3H17.5z M16.1,10c0,0.2,0,0.5,0,0.7c-0.3,2.8-2.6,5.1-5.4,5.4c-0.2,0-0.5,0-0.7,0
              s-0.5,0-0.7,0c-2.8-0.3-5.1-2.6-5.4-5.4c0-0.2,0-0.5,0-0.7s0-0.5,0-0.7c0.3-2.8,2.6-5.1,5.4-5.4c0.2,0,0.5,0,0.7,0s0.5,0,0.7,0
              c2.8,0.3,5.1,2.6,5.4,5.4C16.1,9.5,16.1,9.8,16.1,10z"/>
            <path class="st0" d="M10.6,6.9c-0.2,0-0.4-0.1-0.6-0.1c-0.2,0-0.4,0-0.6,0.1C8.2,7.2,7.2,8.2,6.9,9.4c0,0.2-0.1,0.4-0.1,0.6
              s0,0.4,0.1,0.6c0.2,1.3,1.2,2.2,2.5,2.5c0.2,0,0.4,0.1,0.6,0.1c0.2,0,0.4,0,0.6-0.1c1.3-0.2,2.2-1.2,2.5-2.5
              c0-0.2,0.1-0.4,0.1-0.6s0-0.4-0.1-0.6C12.8,8.2,11.8,7.2,10.6,6.9z"/>
          </svg>
          <span>Use my current location</span>
        </a>
      </div>
    </div>

    <div class="search__map-container">
      <div class="search__map-zoom-button-container">
        <div class="search__map-button-zoom search__map-button-zoom--in"
             @click.stop.prevent="zoomBy(1)"
        >
          <svg class="search__map-button-zoom-icon"
               viewBox="0 0 32 32"
               style="enable-background:new 0 0 32 32;">
            <polygon points="22,14.9 17.1,14.9 17.1,10 14.9,10 14.9,14.9 10,14.9 10,17.1 14.9,17.1 14.9,22 17.1,22 17.1,17.1 22,17.1 "/>
          </svg>
        </div>
        <div class="search__map-button-zoom search__map-button-zoom--out"
             @click.stop.prevent="zoomBy(-1)"
        >
          <div class="search__map-button-zoom-image-container"></div>
          <svg class="search__map-button-zoom-icon"
               viewBox="0 0 32 32">
            <polygon class="zoom-icon" points="22,14.9 10,14.9 10,17.1 22,17.1 " />
          </svg>
        </div>
      </div>

      <div class="search__map"
           @click.stop.prevent=""
      ></div>
    </div>
    <div class="search__jobs-available-container">
      <div class="search__jobs-available"><span>{{searchResults.jobs.length }}</span> <span class="search__jobs-available-title"></span> jobs available:</div>
    </div>
    <div class="search__view-list-container">
      <ul class="search__view-list">
        <li class="search__view-list-element"
            :data-group-id="job.jobLocationGroupId"
            v-for="(job, index) in visibleSearchResults"
            :key="index"
            v-on:click="handleVacancyClick(job.jobLocationGroupId)">
          <job-summary :distance="job.distance"
                       :distance-time="job.distanceTime"
                       :position="job.role"
                       :prison-city="job.prison_location.town"
                       :prison-name="job.prison_name"
                       :prison-page-link="job.url"
                       :salary="job.salary"
                       :selected="job.jobLocationGroupId == searchResults.selectedJobLocationGroupId"
                       :title="job.title"
                       :url="job.url"
          >
          </job-summary>
        </li>
        <li>
          <div class="search__job-list-message"
          >
            <a href="/prison-officer/find-a-job">See other Prison jobs</a>
          </div>
        </li>
      </ul>
      <div class="search__pagination"
           v-if="deviceIsMobile && (numberOfResultPages > 1)">
        <a class="search__pagination-skip-link"
           :class="{'search__pagination-skip-link--enabled': (backwardEnabled == true)}"
           @click.stop.prevent="showFirstPage">
          first</a>
        <button class="search__pagination-direction"
                :class="{'search__pagination-direction--enabled': (backwardEnabled == true)}"
                @click.stop.prevent="showPreviousPage">
          <svg x="0px" y="0px"
               viewBox="0 0 32.9 32.9"
               xml:space="preserve"
          >
              <path class="st0" d="M21.2,11.7v0.9c0,0.2-0.1,0.3-0.2,0.3l-7.5,3.4c-0.1,0-0.1,0.1,0,0.1l7.5,3.4c0.1,0.1,0.2,0.2,0.2,0.3v0.9
                c0,0.2-0.1,0.3-0.3,0.2l-8.9-4.1c-0.1-0.1-0.2-0.2-0.2-0.4V16c0-0.2,0.1-0.3,0.2-0.4l8.9-4.1C21.1,11.4,21.2,11.5,21.2,11.7z"/>
            </svg>
        </button>
        <div class="search__pagination-page-numbers-container">
          <div class="search__pagination-current-page-number">
            {{searchResults.listView.activePage + 1}}
          </div>
          <div class="search__pagination-of">of</div>
          <div class="search__pagination-total-pages">
            {{numberOfResultPages}}
          </div>
        </div>
        <button class="search__pagination-direction"
                :class="{'search__pagination-direction--enabled': (forwardEnabled == true)}"
                @click.stop.prevent="showNextPage">
          <svg x="0px"
               y="0px"
               viewBox="0 0 32.9 32.9"
          >
                <path class="st0" d="M11.7,21.2v-0.9c0-0.2,0.1-0.3,0.2-0.3l7.5-3.4c0.1,0,0.1-0.1,0-0.1L11.9,13c-0.1-0.1-0.2-0.2-0.2-0.3v-0.9
                  c0-0.2,0.1-0.3,0.3-0.2l8.9,4.1c0.1,0.1,0.2,0.2,0.2,0.4v0.9c0,0.2-0.1,0.3-0.2,0.4l-8.9,4.1C11.9,21.5,11.7,21.4,11.7,21.2z"/>

          </svg>
        </button>
        <a class="search__pagination-skip-link"
           :class="{'search__pagination-skip-link--enabled': (forwardEnabled == true)}"
           @click.stop.prevent="showLastPage"
        >last</a>
      </div>

    </div>
  </div>
</template>

<script>
  import axios from 'axios';
  import CustomMarker from '../js/CustomMarker';
  import dummyJobs from '../js/dummyJobs';

  export default {
    props: {

      'title': {
        default: '',
        type: String
      },
      'jobFeedUrl': {
        default: '',
        type: String
      },
      'jobListMessage': {
        default: '',
        type: String
      },
      'jobListMessageURL': {
        default: '',
        type: String
      }
    },

    data() {
      return {
        deviceIsMobile: false,

        geoLocationIsAvailable: false,
        geoLocationIsBusy: false,
        geoLocationIsActive: false,

        vacanciesDataURL: this.jobFeedUrl,

        searchResults: {
          urlEncodedPostCode: '',
          googleMapAPIKey: 'AIzaSyDDplfBkLzNA3voskfGyExYnQ46MJ0VtpA',
          listView: {
            activePage: 0,
            resultsPerPage: 5,
            forwardEnabled: true,
            backwardEnabled: false
          },
          jobs: [], //dummyJobs,
          jobLocationGroups: {},
          orderedJobLocationGroups: [],
          searchTerm: this.storeGet('searchResults.searchTerm') || '',
          searchTermMarker: {},
          selectedJobLocationGroupId: '',
          visibleJobLocationGroup: null,
        },

        mapSrc: '',

        defaultZoomLevel: 7,
        defaultMobileZoomLevel: 6,
        defaultZoomInAmount: 2,
        initialZoomFlag: false,
        maxZoom: 25,
        minZoom: 5,

        mapOptions: {
          zoom: 7,
          center: new google.maps.LatLng(52.4832138, -1.5947146),
          disableDefaultUI: false,
          streetViewControl: false,
          fullscreenControl: false,
          mapTypeControl: false,
          gestureHandling: 'greedy',
          zoomControl: false,
          zoomControlOptions: {
            position: google.maps.ControlPosition.TOP_RIGHT
          },
        },

        titleText: this.title,

        placeHolderText: 'e.g. SW1A 2LW, Birmingham or Essex',

        mounted: false,
      };
    },

    methods: {

      alert(title, message){
        alert(title + "\n" + message);
      },

      isDeviceMobile() {
        return (window.innerWidth < 768);
      },

      toRadians(degrees) {
        return degrees * (Math.PI / 180);
      },

      calculateDistanceBetweenTwoLatLngPoints(lat1, lng1, lat2, lng2) {
        const
          R = 6371 // kilometres
            * 0.621371 // kilometres per mile
          ,
          φ1 = this.toRadians(lat1),
          φ2 = this.toRadians(lat2),
          Δφ = this.toRadians(lat2 - lat1),
          Δλ = this.toRadians(lng2 - lng1)
        ;

        const a = Math.sin(Δφ / 2) * Math.sin(Δφ / 2) +
          Math.cos(φ1) * Math.cos(φ2) *
          Math.sin(Δλ / 2) * Math.sin(Δλ / 2);

        const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
        const d = R * c;
        return d;
      },

      updateSelectedJobLocationGroupId(groupId) {
        this.searchResults.selectedJobLocationGroupId = groupId;
      },

      calculateActivePageFromGroupId(groupId) {
        const groups = this.searchResults.orderedJobLocationGroups;
        for (let i in groups) {
          i = parseInt(i);
          if (groups[i][0].jobLocationGroupId == groupId) {
            this.searchResults.listView.activePage = Math.floor(i / this.searchResults.listView.resultsPerPage);
            return;
          }
        }
      },

      convertGroupIdToCoords(groupId) {
        const array = groupId.split(',');
        return {lat: array[0], lng: array[1]};
      },

      zoomTo(level) {
        if (level >= this.minZoom && level <= this.maxZoom) {
          this.map.setZoom(level);
        }
      },

      zoomBy(amount) {
        this.zoomTo(this.map.getZoom() + amount);
      },

      initialZoom() {
        if (!this.initialZoomFlag) {
          this.initialZoomFlag = true;
          this.zoomTo(this.defaultZoomLevel + this.defaultZoomInAmount);
        }
      },

      focusOnJobLocationGroup(groupId) {
        this.updateSelectedJobLocationGroupId(groupId);
        CustomMarker.changeSelectedMarkerByGroupId(groupId);

        const coords = this.convertGroupIdToCoords(groupId);
        this.recenterMap(coords.lat, coords.lng);
        this.initialZoom();
      },

      recenterMap(lat, lng) {
        this.map.panTo(new google.maps.LatLng(lat, lng));
      },

      handleMapMarkerClick(self, groupId, event) {
        if (self.deviceIsMobile) {
          self.calculateActivePageFromGroupId(groupId);
        } else {
          document.querySelector(`.search__view-list-element[data-group-id='${groupId}']`)
            .scrollIntoView({ behavior: 'smooth' });
        }
        self.focusOnJobLocationGroup(groupId);
        event.preventDefault();
        event.stopPropagation();
        return false;
      },

      handleVacancyClick(groupId) {
        this.focusOnJobLocationGroup(groupId);
      },

      handleGlobalSearchClick() {
        this.updateSelectedJobLocationGroupId('');

        CustomMarker.deselectMarker();
      },

      updateMapWithJobLocationGroupMarkers(jobLocationGroups) {
        const markerArgs = [];
        for (let group in jobLocationGroups) {
          markerArgs.push({
            class: 'search__map-marker--job-location-group',
            solid: true,
            amount: jobLocationGroups[group].jobs.length,
            groupId: group,
            clickCallback: this.handleMapMarkerClick.bind(null, this, group),
            prisonName: jobLocationGroups[group].prisonName
          });
        }
        for (let i in markerArgs) {
          if (markerArgs[i].groupId == this.searchResults.selectedJobLocationGroupId) {
            markerArgs[i].selected = true;
          }
          const latLngArr = markerArgs[i].groupId.split(',');
          const latLng = new google.maps.LatLng(latLngArr[0], latLngArr[1]);
          const marker = new CustomMarker(
            latLng,
            this.map,
            markerArgs[i]
          );
        }
      },

      updateJobsDistance(lat, lng) {

        // calculate distance
        for (let i = 0; i < this.searchResults.jobs.length; i++) {

          const newDistance = this.calculateDistanceBetweenTwoLatLngPoints(
            lat,
            lng,
            this.searchResults.jobs[i].prison_location.lat,
            this.searchResults.jobs[i].prison_location.lng
          );

          this.searchResults.jobs[i].distance = newDistance;
        }

        this.searchResults.jobs.sort(function (a, b) {
          return a.distance - b.distance;
        });

        this.searchResults.orderedJobLocationGroups = [];
        for (const group in this.searchResults.jobLocationGroups) {
          const newDistance = this.calculateDistanceBetweenTwoLatLngPoints(
            lat,
            lng,
            this.searchResults.jobLocationGroups[group].jobs[0].prison_location.lat,
            this.searchResults.jobLocationGroups[group].jobs[0].prison_location.lng
          );
          this.searchResults.jobLocationGroups[group].distance = newDistance;
          this.searchResults.orderedJobLocationGroups.push(this.searchResults.jobLocationGroups[group].jobs);
        }

        this.searchResults.orderedJobLocationGroups.sort(function (a, b) {
          return a[0].distance - b[0].distance;
        });
      },

      createJobLocationGroups() {

        // iterate over the jobs and put them in the correct jobLocationGroup
        const jobs = this.searchResults.jobs;
        const jobLocationGroups = {};
        let closestJobLocationGroupDistance = Number.MAX_SAFE_INTEGER;
        let closestJobLocationGroupId = null;

        for (let i = 0; i < jobs.length; i++) {
          const latLngStr = jobs[i].prison_location.lat + ',' + jobs[i].prison_location.lng;

          if (typeof jobLocationGroups[latLngStr] == 'undefined') {
            jobLocationGroups[latLngStr] = {
              prisonName: jobs[i].prison_name,
              jobs: [jobs[i]]
            };
          } else {
            jobLocationGroups[latLngStr].jobs.push(jobs[i]);
          }

          jobs[i].jobLocationGroupId = latLngStr;

          if (jobs[i].distance < closestJobLocationGroupDistance) {
            closestJobLocationGroupDistance = jobs[i].distance;
            closestJobLocationGroupId = latLngStr;
          }
        }
        this.searchResults.jobLocationGroups = jobLocationGroups;
        this.searchResults.selectedJobLocationGroupId = closestJobLocationGroupId;

        // initialize orderedJobLocationGroups array
        this.searchResults.orderedJobLocationGroups = [];
        for (const id in this.searchResults.jobLocationGroups) {
          this.searchResults.orderedJobLocationGroups.push(this.searchResults.jobLocationGroups[id].jobs);
        }

        // initially sort the job location groups by town name
        this.searchResults.orderedJobLocationGroups.sort(function (a, b) {
          const
            aTown = a[0].prison_location.town,
            bTown = b[0].prison_location.town
          ;
          if (aTown < bTown) {
            return -1;
          } else {
            if (aTown > bTown) {
              return 1;
            } else {
              return 0;
            }
          }
        });

        this.updateMapWithJobLocationGroupMarkers(this.searchResults.jobLocationGroups);
      },

      createMap() {
        if (this.isDeviceMobile()) {
          this.mapOptions.zoom = this.defaultMobileZoomLevel;
        } else {
          this.mapOptions.zoom = this.defaultZoomLevel;
        }

        this.map = new google.maps.Map(
          document.getElementsByClassName('search__map')[0]
          , this.mapOptions
        );
      },

      deleteSearchTermMarker() {
        const element = document.getElementsByClassName('search__map-marker--search-term')[0];
        if (typeof element !== 'undefined') {
          element.parentNode.removeChild(element);
        }
      },

      updateSearchTermMarker(lat, lng) {
        this.geoLocationIsActive = false;

        this.deleteSearchTermMarker();

        this.searchResults.searchTermMarker.markerDiv = new CustomMarker(
          new google.maps.LatLng(lat, lng),
          this.map,
          {class: 'search__map-marker--search-term'}
        );

      },

      handleNewSearchLocation(lat, lng) {
        this.searchResults.listView.activePage = 0;
        this.updateJobsDistance(lat, lng);
        this.updateSearchTermMarker(lat, lng);
        this.recenterMap(lat, lng);
        this.initialZoomflag = false;
        this.initialZoom();
      },

      processGeocoderResults(results, status) {
        if (status == google.maps.GeocoderStatus.OK) {
          this.handleNewSearchLocation(
            results[0].geometry.location.lat(),
            results[0].geometry.location.lng()
          );
        } else {
          let msg = '';
          if (status === 'ZERO_RESULTS') {
            this.alert('No results found for ' + this.searchResults.searchTerm, 'Try searching again');
          } else {
            // TODO need proper content for this:
            msg = 'Problem communicating with Google Geocoder API, ' + status ;
            this.alert('', msg);
          }
          console.error(status);
          console.dir(results);
        }
      },

      handleSearchInputKeyPress(e) {
        if(e.keyCode === 13){
          e.preventDefault();
          this.search();
        }
      },

      handleClearSearchClick() {
        this.searchResults.searchTerm = '';
        this.deleteSearchTermMarker();
        document.getElementsByClassName('search__input')[0].focus();
      },

      search() {
        if (this.searchResults.searchTerm) {
          new google.maps.Geocoder().geocode(
            {'address': 'UK ' + this.searchResults.searchTerm},
            this.processGeocoderResults
          );
          document.getElementsByClassName('search__input')[0].blur();
        }
      },

      useGeoLocation() {
        this.searchResults.searchTerm = '';
        this.geoLocationIsBusy = true;
        navigator.geolocation.getCurrentPosition(
          position => {
            this.handleNewSearchLocation(position.coords.latitude, position.coords.longitude);
            this.geoLocationIsBusy = false;
            this.geoLocationIsActive = true;
          },
          error => {
            if (typeof error.code !== 'undefined' && error.code === 1) {
              this.alert('Sorry, we can’t detect your location.', 'Try searching by postcode, town or region.');
            } else {
              this.alert('', 'Problem using Geolocation');
            }
            console.dir(error);
          }
        );
      },

      showPage(i) {
        this.searchResults.listView.activePage = i
      },

      showNextPage() {
        if (this.searchResults.listView.activePage < (this.numberOfResultPages - 1)) {
          this.searchResults.listView.activePage++
        }
      },

      showPreviousPage() {
        if (this.searchResults.listView.activePage > 0) {
          this.searchResults.listView.activePage--
        }
      },

      showFirstPage() {
        this.showPage(0);
      },

      showLastPage() {
        this.showPage(this.numberOfResultPages - 1);
      },

      handleGotVacanciesData(response) {
        const self = this;
        self.searchResults.jobs = response.data;
        //self.searchResults.jobs = dummyJobs;

        if (self.searchResults.searchTerm) {
          self.search();
        } else {

        }
      },

      updateIsDeviceMobile() {
        this.deviceIsMobile = this.isDeviceMobile();
      },

      handleScreenResize() {
        this.updateIsDeviceMobile();
      },

      storeSave(key, value) {
        localStorage.setItem('ppj.find-a-job:' + key, value);
      },

      storeGet(key) {
        return localStorage.getItem('ppj.find-a-job:' + key);
      },

      restorePageData() {
        if (this.searchResults.searchTerm) {
          this.search();
        }

      }

    },

    watch: {
      'searchResults.searchTerm': function(val) {
        this.storeSave('searchResults.searchTerm', val);
      }
    },

    computed: {
      numberOfResultPages: function () {
        const num = Math.ceil(this.searchResults.orderedJobLocationGroups.length / this.searchResults.listView.resultsPerPage);
        return num;
      },

      defaultSearchResults: function () {
        return this.searchResults.jobs;
      },

      backwardEnabled: function () {
        return (this.searchResults.listView.activePage > 0);
      },

      forwardEnabled: function () {
        return (this.searchResults.listView.activePage < (this.numberOfResultPages - 1));
      },

      visibleSearchResults: function() {
        if (this.mounted) {

          if (Object.keys(this.searchResults.jobLocationGroups).length === 0) {
            this.createJobLocationGroups();
          }

          let selectedJobLocationGroups = null;
          if (this.deviceIsMobile) {
            selectedJobLocationGroups = this.searchResults.orderedJobLocationGroups.slice().splice(
              this.searchResults.listView.resultsPerPage * this.searchResults.listView.activePage,
              this.searchResults.listView.resultsPerPage
            );
          } else {
            selectedJobLocationGroups = this.searchResults.orderedJobLocationGroups;
          }

          const results = [];
          for (const i in selectedJobLocationGroups) {
            for (const j in selectedJobLocationGroups[i]) {
              results.push(selectedJobLocationGroups[i][j]);
            }
          }

          return results;
        }

      }
    },

    created() {
      this.updateIsDeviceMobile();

      if ("geolocation" in navigator) {
        this.geoLocationIsAvailable = true;
      }
    },

    mounted() {

      const self = this;
      this.createMap();
      this.mounted = true;

      window.addEventListener('resize', this.handleScreenResize);

      console.log('job list message: ' , this.jobListMessage);
      console.log('job list message URL: ' , this.jobListMessageURL);

      axios.get(this.vacanciesDataURL,  { responseType: 'json' })
        .then(
         /* response => {

          //self.searchResults.jobs = response.data;
          self.searchResults.jobs = dummyJobs;

          if (self.searchResults.searchTerm) {
            self.search();
          } else {

          }
        }*/
          self.handleGotVacanciesData
        )
        .catch(function (error) {
          console.log(error);
        });

      //self.handleGotVacanciesData();

      this.restorePageData();
    }
  }
</script>

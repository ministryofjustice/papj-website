<template>
    <div class="header"
         :class="{
           'header--nav-menu-open': menuOpen,
           'header--style-dark': (headerStyle == 'dark')
         }"
         v-cloak
    >
        <div class="l-site-header">
            <div class="header__site-header">
              <div class="header__site-header-content">
                <a href="/" class="header__logo"></a>
                <nav-link v-on:request-open-nav-menu="openNavMenu"></nav-link>
                <nav-menu :menu-data='menuData' v-on:request-close-nav-menu="closeNavMenu"></nav-menu>
              </div>
              <h1 class="header__site-header-title">Prison and Probation Jobs</h1>
            </div>
        </div>
        <div class="header__hero">
          <div class="header__img-ratio">
            <div class="header__img-container"
            >
                <img class="header__image"
                     v-if="processedHeaderImageData"
                     :src='processedHeaderImageData.url'
                     :srcset='processedHeaderImageData.srcset'
                     :alt='processedHeaderImageData.alt'
                     :sizes='processedHeaderImageData.sizes'
                     @load='handleImageLoaded'
                     :class="{'header__image--loaded': this.heroImageLoaded}"
                />
              <!--              <picture v-if="processedHeaderImageData">
                            <source v-for="source in processedHeaderImageData.sources"
                                     :media="source.media"
                                     :srcet="source.srcset"
                             ></source>
                             <img class="header__image"
                                  :src="processedHeaderImageData.url"
                                  :alt="processedHeaderImageData.alt"
                             >
                           </picture>-->
            </div>
            <div class="header__overlay">
                <div class="l-full">
                  <div class="header__text-container">
                      <div class="header__text"
                           v-if="hasDefaultSlot"
                      ><slot></slot></div>
                      <div class="header__subtext"
                           v-if="headerSubtext">
                          {{headerSubtext}}
                      </div>
                  </div>
                </div>
            </div>
          </div>
        </div>

    </div>
</template>
<script>
    import Vue from 'vue';
    import Slick from 'vue-slick';

    const NavLink = {
        template:
            `<div class="header__nav-link js" @click="openNavMenu">
                MENU
            </div>`,

        methods: {
            openNavMenu: function() {
                this.$emit('request-open-nav-menu', true);
            }
        }
    };

    const NavMenu = {
        template:
            ` <div class="header__nav-menu">
                <button class="header__nav-menu-close-button js" @click="closeNavMenu"></button>
                <ul class="header__nav-menu-list">
                    <li v-for="item in menuData"
                        class="header__nav-menu-list-element"
                        :class="{'header__nav-menu-list-element--selected' : item.selected}">
                        <a :href="item.url">
                          <span class="header__nav-menu-list-element-text">{{item['title']}}</span>
                        </a>
                    </li>
                </ul>
            </div>`,

        methods: {
            closeNavMenu: function() {
                this.$emit('request-close-nav-menu', true);
            }
        },

        mounted() {
        },

        props: ['menu-data']
    };

    export default {
        props: {
            'title': {
                default: ''
            },
            'subtitle': {
                default: ''
            },
            'header-style': {
                default: 'dark'
            },
            'header-subtext': {
                default: ''
            },
            'background-image': {
                default: true
            },
            'header-image': {
              default: ''
            },
            'header-image-mobile': {
              default: ''
            },
            'menu-data': {
                default: {}
            }
        },

        data() {
          return {
            heroImageLoaded: false,
            menuOpen: false
          }
        },

        computed:  {
          'hasDefaultSlot': function() {
            return !!this.$slots.default
          },

          'processedHeaderImageData': function() {
            if (this.headerImage) {

              const img = {};

              const breakpointWidths = {
                mobile: 320,
                portrait: 768,
                landscape: 1024,
                large: 1440
              };

              let breakpointNames = Object.keys(breakpointWidths);

              const home = (this.headerStyle === 'home') ? '-home' : '';

              // construct srcset
              let srcset = '';
              if (this.headerImageMobile) {
                //console.log('headerImageMobile');
                //console.dir(this.headerImageMobile);
                const imageDataMobile = JSON.parse(this.headerImageMobile);

                for (const bp of breakpointNames.slice(0,2)) {
                  srcset += this.createSrcsetLine(imageDataMobile, bp, home, true);
                }

                breakpointNames = breakpointNames.splice(2);
              }

              const imageData = JSON.parse(this.headerImage);
              for (const bp of breakpointNames) {
                srcset += this.createSrcsetLine(imageData, bp, home, true);
              }

              img.srcset = srcset;

//              for (const bp of breakpoints) {
//                //srcset += this.createSrcsetLine(imageData, bp, home);
//                img.sources.push({
//                  media: imageData.sizes['header-' + breakpoint + home],
//                  ,
//                  srcset: ''
//                });
//              }
//
//              img.sources = [];
//              let i = 0;
//
//              if (this.headerImageMobile) {
//                const imageDataMobile = JSON.parse(this.headerImageMobile);
//                do {
//                  const breakpoint = breakpointNames[i];
//                  img.sources.push({
//                      srcset: this.createSrcsetLine(imageDataMobile, breakpoint, home),
//                      media: "(max-width: " + breakpointWidths[breakpoint] + "px)"
//                  });
//                } while( ++i < 2);
//              }
//
//              const imageData = JSON.parse(this.headerImage);
//              do {
//                const breakpoint = breakpointNames[i];
//                img.sources.push({
//                  srcset: this.createSrcsetLine(imageData, breakpoint, home),
//                  media: "(max-width: " + breakpointWidths[breakpoint] + "px)"
//                });
//              } while( ++i < breakpointNames.length);

              // construct src
              img.url = imageData.url;

              // construct alt
              img.alt = imageData.alt;

              //img.sizes = '(max-width: 320px) 320px, (max-width: 768px) 768px, (max-width: 1024px) 1024px, (max-width: 1440px) 1440px';
              return img;
            }
          }
        },

        components: {
            'nav-menu': NavMenu,
            'nav-link': NavLink,
            Slick
        },

        methods: {
            createSrcsetLine(imageData, breakpoint, home, width=false) {
              let srcset = imageData.sizes['header-' + breakpoint + home];
              if (width) {
                srcset += ' ' + imageData.sizes['header-' + breakpoint + home + '-width'] + 'w, ';
              }
              //console.log('srcset line ', srcset);
              return srcset;
            },
            openNavMenu() {
                this.menuOpen = true;
                document.getElementsByTagName('body')[0].style.overflow = 'hidden';
            },
            closeNavMenu(){
                this.menuOpen = false;
                document.getElementsByTagName('body')[0].style.overflow = '';
            },
          handleImageLoaded() {
              this.heroImageLoaded = true;
          }
        }
    }
</script>

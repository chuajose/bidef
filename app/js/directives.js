/**
 * INSPINIA - Responsive Admin Theme
 * Copyright 2014 Webapplayers.com
 *
 * Main directives.js file
 * Define directives for used plugin
 *
 *
 * Functions (directives)
 *  - pageTitle
 *  - sideNavigation
 *  - iboxTools
 *  - minimalizaSidebar
 *  - vectorMap
 *  - morrisArea
 *  - morrisBar
 *  - morrisLine
 *  - morrisDonut
 *  - sparkline
 *  - icheck
 *  - ionRangeSlider
 *  - dropZone
 *  - fancyBox
 *  - responsiveVideo
 *
 */


/**
 * pageTitle - Directive for set Page title - mata title
 */
function pageTitle($rootScope, $timeout) {
    return {
        link: function(scope, element) {
            var listener = function(event, toState, toParams, fromState, fromParams) {
                // Default title - load on Dashboard 1
                var title = 'Bidef APP';
                // Create your own title pattern
                if (toState.data && toState.data.pageTitle) title = 'Bidef | ' + toState.data.pageTitle;
                $timeout(function() {
                    element.text(title);
                });
            };
            $rootScope.$on('$stateChangeStart', listener);
        }
    }
};

/**
 * sideNavigation - Directive for run metsiMenu on sidebar navigation
 */
function sideNavigation() {
    return {
        restrict: 'A',
        link: function(scope, element) {
            // Call the metsiMenu plugin and plug it to sidebar navigation
            element.metisMenu();
        }
    };
};

/**
 * responsibleVideo - Directive for responsive video
 */
function responsiveVideo() {
    return {
        restrict: 'A',
        link:  function(scope, element) {
            var figure = element;
            var video = element.children();
            video
                .attr('data-aspectRatio', video.height() / video.width())
                .removeAttr('height')
                .removeAttr('width')

            //We can use $watch on $window.innerWidth also.
            $(window).resize(function() {
                var newWidth = figure.width();
                video
                    .width(newWidth)
                    .height(newWidth * video.attr('data-aspectRatio'));
            }).resize();
        }
    }
}

/**
 * iboxTools - Directive for iBox tools elements in right corner of ibox
 */
function iboxTools($timeout) {
    return {
        restrict: 'A',
        scope: true,
        templateUrl: 'views/common/ibox_tools.html',
        controller: function ($scope, $element) {
            // Function for collapse ibox
            $scope.showhide = function () {
                var ibox = $element.closest('div.ibox');
                var icon = $element.find('i:first');
                var content = ibox.find('div.ibox-content');
                content.slideToggle(200);
                // Toggle icon from up to down
                icon.toggleClass('fa-chevron-up').toggleClass('fa-chevron-down');
                ibox.toggleClass('').toggleClass('border-bottom');
                $timeout(function () {
                    ibox.resize();
                    ibox.find('[id^=map-]').resize();
                }, 50);
            },
                // Function for close ibox
                $scope.closebox = function () {
                    var ibox = $element.closest('div.ibox');
                    ibox.remove();
                }
        }
    };
};

function mailboxTools($timeout) {
    return {
        restrict: 'A',
        scope: true,
        templateUrl: 'views/webmail/mailbox_tools.html',
        controller: function ($scope, $element) {
            // Function for collapse ibox
            $scope.showhide = function () {
                var ibox = $element.closest('div.ibox');
                var icon = $element.find('i:first');
                var content = ibox.find('div.ibox-content');
                content.slideToggle(200);
                // Toggle icon from up to down
                icon.toggleClass('fa-chevron-up').toggleClass('fa-chevron-down');
                ibox.toggleClass('').toggleClass('border-bottom');
                $timeout(function () {
                    ibox.resize();
                    ibox.find('[id^=map-]').resize();
                }, 50);
            },
                // Function for close ibox
                $scope.closebox = function () {
                    var ibox = $element.closest('div.ibox');
                    ibox.remove();
                }
        }
    };
};

/**
 * minimalizaSidebar - Directive for minimalize sidebar
*/
function minimalizaSidebar($timeout) {
    return {
        restrict: 'A',
        template: '<a class="navbar-minimalize minimalize-styl-2 btn btn-primary " href="" ng-click="minimalize()"><i class="fa fa-bars"></i></a>',
        controller: function ($scope, $element) {
            $scope.minimalize = function () {
                $("body").toggleClass("mini-navbar");
                if (!$('body').hasClass('mini-navbar') || $('body').hasClass('body-small')) {
                    // Hide menu in order to smoothly turn on when maximize menu
                    $('#side-menu').hide();
                    // For smoothly turn on menu
                    $timeout(function () {
                        $('#side-menu').fadeIn(500);
                        }, 100);
                } else {
                    // Remove all inline style from jquery fadeIn function to reset menu state
                    $('#side-menu').removeAttr('style');
                }
            }
        }
    };
};

/**
 * vectorMap - Directive for Vector map plugin
 */
function vectorMap() {
    return {
        restrict: 'A',
        scope: {
            myMapData: '=',
        },
        link: function (scope, element, attrs) {
            var mapElement = element.vectorMap({
                map: 'es_mill_en',
                regionsSelectable: true,
                backgroundColor: "transparent",
                regionStyle: {
                    initial: {
                        fill: '#e4e4e4',
                        "fill-opacity": 0.9,
                        stroke: 'none',
                        "stroke-width": 0,
                        "stroke-opacity": 0
                    },
                    selected: {
                        fill: '#1ab394'
                    }
                },
                series: {
                    regions: [
                        {
                            values: scope.myMapData,
                            scale: {
                              1: "#1c84c6",
                              2: "#111111"
                            },
                            normalizeFunction: 'polynomial'
                        }
                    ]
                },
                onRegionSelected: function(e, code, isSelected, selectedRegions){
                    console.log(selectedRegions);
                },
                onRegionOver:function(e, code){
                    console.log(code);
                }
            });
        }
    }
}


/**
 * morrisArea - Directive for Morris chart - type Area
 */
function morrisArea() {
    return {
        restrict: 'A',
        scope: {
            chartOptions: '='
        },
        link: function(scope, element, attrs) {
            var chartDetail = scope.chartOptions;
            chartDetail.element = attrs.id;
            var chart = new Morris.Area(chartDetail);
            return chart;
        }
    }
}

/**
 * morrisBar - Directive for Morris chart - type Bar
 */
function morrisBar() {
    return {
        restrict: 'A',
        scope: {
            chartOptions: '='
        },
        link: function(scope, element, attrs) {
            var chartDetail = scope.chartOptions;
            chartDetail.element = attrs.id;
            var chart = new Morris.Bar(chartDetail);
            return chart;
        }
    }
}

/**
 * morrisLine - Directive for Morris chart - type Line
 */
function morrisLine() {
    return {
        restrict: 'A',
        scope: {
            chartOptions: '='
        },
        link: function(scope, element, attrs) {
            var chartDetail = scope.chartOptions;
            chartDetail.element = attrs.id;
            var chart = new Morris.Line(chartDetail);
            return chart;
        }
    }
}

/**
 * morrisDonut - Directive for Morris chart - type Donut
 */
function morrisDonut() {
    return {
        restrict: 'A',
        scope: {
            chartOptions: '='
        },
        link: function(scope, element, attrs) {
            var chartDetail = scope.chartOptions;
            chartDetail.element = attrs.id;
            var chart = new Morris.Donut(chartDetail);
            return chart;
        }
    }
}

/**
 * sparkline - Directive for Sparkline chart
 */
function sparkline() {
    return {
        restrict: 'A',
        scope: {
            sparkData: '=',
            sparkOptions: '=',
        },
        link: function (scope, element, attrs) {
            scope.$watch(scope.sparkData, function () {
                render();
            });
            scope.$watch(scope.sparkOptions, function(){
                render();
            });
            var render = function () {
                $(element).sparkline(scope.sparkData, scope.sparkOptions);
            };
        }
    }
};

/**
 * icheck - Directive for custom checkbox icheck
 */
function icheck($timeout) {
    return {
        restrict: 'A',
        require: 'ngModel',
        link: function($scope, element, $attrs, ngModel) {
            return $timeout(function() {
                var value;
                value = $attrs['value'];

                $scope.$watch($attrs['ngModel'], function(newValue){
                    $(element).iCheck('update');
                })

                return $(element).iCheck({
                    checkboxClass: 'icheckbox_square-green',
                    radioClass: 'iradio_square-green'

                }).on('ifChanged', function(event) {
                        if ($(element).attr('type') === 'checkbox' && $attrs['ngModel']) {
                            $scope.$apply(function() {
                                return ngModel.$setViewValue(event.target.checked);
                            });
                        }
                        if ($(element).attr('type') === 'radio' && $attrs['ngModel']) {
                            return $scope.$apply(function() {
                                return ngModel.$setViewValue(value);
                            });
                        }
                    });
            });
        }
    };
}

/**
 * ionRangeSlider - Directive for Ion Range Slider
 */
function ionRangeSlider() {
    return {
        restrict: 'A',
        scope: {
            rangeOptions: '='
        },
        link: function (scope, elem, attrs) {
            elem.ionRangeSlider(scope.rangeOptions);
        }
    }
}

/**
 * dropZone - Directive for Drag and drop zone file upload plugin
 */
function dropZone($http) {

    return function(scope, element, attrs) {
        scope.files=[];
        element.dropzone({
            url: "../api/index.php/email/attachment",
            addRemoveLinks:true,
            maxFilesize: 10,
            paramName: "uploadfile",
            maxThumbnailFilesize: 5,
            init: function() {
                this.on('success', function(file, json) {

                    file.nameserver=json.file.name;
                    file.url=json.file.url;
                    scope.files.push(file);
                });
                this.on('addedfile', function(file) {
                    scope.$apply(function(){
                      //  console.log(file);
                        //scope.files.push(file);
                    });
                });
                this.on('drop', function(file) {
                   // alert('file');
                });
                this.on('removedfile', function(file) {

                   // console.log(json);
                    //al eliminar fichero, buscamos su posicion dentro del array para borrarlo
                    var position =scope.files.indexOf(file);
                    scope.files.splice(position,1);

                    return $http({
                                    url     : "../api/index.php/email/attachment",
                                    method  : "delete",
                                    data    : "name="+file.nameserver,
                                    //data    : 
                                   // headers : {'Content-Type': 'application/x-www-form-urlencoded'}
                                })
                                .success(function(data){
                                
                                })
                                .error(function(){
                                    //$location.path("/")
                            });

                    
                });
                
            }
        });
    }
}

/**
 * fancyBox - Directive for Fancy Box plugin used in simple gallery view
 */
function fancyBox() {
    return {
        restrict: 'A',
        link: function(scope, element) {
            element.fancybox({
                openEffect	: 'none',
                closeEffect	: 'none'
            });
        }
    }
}

/**
 *
 * Pass all functions into module
 */
angular
    .module('bidef')
    .directive('pageTitle', pageTitle)
    .directive('sideNavigation', sideNavigation)
    .directive('iboxTools', iboxTools)
    .directive('mailboxTools', mailboxTools)
    .directive('minimalizaSidebar', minimalizaSidebar)
    .directive('vectorMap', vectorMap)
    .directive('morrisArea', morrisArea)
    .directive('morrisBar', morrisBar)
    .directive('morrisLine', morrisLine)
    .directive('morrisDonut', morrisDonut)
    .directive('sparkline', sparkline)
    .directive('icheck', icheck)
    .directive('ionRangeSlider', ionRangeSlider)
    .directive('dropZone', dropZone)
    .directive('fancyBox', fancyBox)
    .directive('responsiveVideo', responsiveVideo)

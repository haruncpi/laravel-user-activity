<!DOCTYPE html>
<html ng-app="myApp">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>User Activity</title>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js"
            crossorigin="anonymous"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/angularjs/1.5.10/angular.min.js"></script>
    <script>
        angular.module('__paging', []).directive('paging', function () {

            var regex = /\{page\}/g;

            return {
                // Restrict to elements and attributes
                restrict: 'EA',

                // Assign the angular link function
                link: fieldLink,

                // Assign the angular directive template HTML
                template: fieldTemplate,

                // Assign the angular scope attribute formatting
                scope: {
                    page: '=',
                    pageSize: '=',
                    total: '=',
                    disabled: '@',
                    dots: '@',
                    ulClass: '@',
                    activeClass: '@',
                    disabledClass: '@',
                    adjacent: '@',
                    pagingAction: '&',
                    pgHref: '@',
                    textFirst: '@',
                    textLast: '@',
                    textNext: '@',
                    textPrev: '@',
                    textFirstClass: '@',
                    textLastClass: '@',
                    textNextClass: '@',
                    textPrevClass: '@',
                    textTitlePage: '@',
                    textTitleFirst: '@',
                    textTitleLast: '@',
                    textTitleNext: '@',
                    textTitlePrev: '@'
                }

            };

            function fieldLink(scope, el, attrs) {
                scope.$watchCollection('[page,pageSize,total,disabled]', function () {
                    build(scope, attrs);
                });
            }

            function fieldTemplate(el, attrs) {
                return '<ul data-ng-hide="Hide" data-ng-class="ulClass"> ' +
                    '<li ' +
                    'title="@{{Item.title}}" ' +
                    'data-ng-class="Item.liClass" ' +
                    'data-ng-repeat="Item in List"> ' +
                    '<a ' +
                    (attrs.pgHref ? 'data-ng-href="@{{Item.pgHref}}" ' : 'href ') +
                    'data-ng-class="Item.aClass" ' +
                    'data-ng-click="Item.action()" ' +
                    'data-ng-bind="Item.value">' +
                    '</a> ' +
                    '</li>' +
                    '</ul>'
            }

            function setScopeValues(scope, attrs) {

                scope.List = [];
                scope.Hide = false;

                scope.page = parseInt(scope.page) || 1;
                scope.total = parseInt(scope.total) || 0;
                scope.adjacent = parseInt(scope.adjacent) || 2;

                scope.pgHref = scope.pgHref || '';
                scope.dots = scope.dots || '...';

                scope.ulClass = scope.ulClass || 'pagination';
                scope.activeClass = scope.activeClass || 'active';
                scope.disabledClass = scope.disabledClass || 'disabled';

                scope.textFirst = scope.textFirst || 'First';
                scope.textLast = scope.textLast || 'Last';
                scope.textNext = scope.textNext || 'Next';
                scope.textPrev = scope.textPrev || 'Prev';

                scope.textFirstClass = scope.textFirstClass || '';
                scope.textLastClass = scope.textLastClass || '';
                scope.textNextClass = scope.textNextClass || '';
                scope.textPrevClass = scope.textPrevClass || '';

                scope.textTitlePage = scope.textTitlePage || 'Page {page}';
                scope.textTitleFirst = scope.textTitleFirst || 'First Page';
                scope.textTitleLast = scope.textTitleLast || 'Last Page';
                scope.textTitleNext = scope.textTitleNext || 'Next Page';
                scope.textTitlePrev = scope.textTitlePrev || 'Previous Page';

                scope.hideIfEmpty = evalBoolAttribute(scope, attrs.hideIfEmpty);
                scope.showPrevNext = evalBoolAttribute(scope, attrs.showPrevNext);
                scope.showFirstLast = evalBoolAttribute(scope, attrs.showFirstLast);
                scope.scrollTop = evalBoolAttribute(scope, attrs.scrollTop);
                scope.isDisabled = evalBoolAttribute(scope, attrs.disabled);
            }

            function evalBoolAttribute(scope, value) {
                return angular.isDefined(value)
                    ? !!scope.$parent.$eval(value)
                    : false;
            }

            function validateScopeValues(scope, pageCount) {

                // Block where the page is larger than the pageCount
                if (scope.page > pageCount) {
                    scope.page = pageCount;
                }

                // Block where the page is less than 0
                if (scope.page <= 0) {
                    scope.page = 1;
                }

                // Block where adjacent value is 0 or below
                if (scope.adjacent <= 0) {
                    scope.adjacent = 2;
                }

                // Hide from page if we have 1 or less pages
                // if directed to hide empty
                if (pageCount <= 1) {
                    scope.Hide = scope.hideIfEmpty;
                }
            }

            function internalAction(scope, page) {

                // Block clicks we try to load the active page
                if (scope.page == page) {
                    return;
                }

                // Block if we are forcing disabled
                if (scope.isDisabled) {
                    return;
                }

                // Update the page in scope
                scope.page = page;

                // Pass our parameters to the paging action
                scope.pagingAction({
                    page: scope.page,
                    pageSize: scope.pageSize,
                    total: scope.total
                });

                // If allowed scroll up to the top of the page
                if (scope.scrollTop) {
                    scrollTo(0, 0);
                }
            }

            function addPrevNext(scope, pageCount, mode) {

                // Ignore if we are not showing
                // or there are no pages to display
                if ((!scope.showPrevNext && !scope.showFirstLast) || pageCount < 1) {
                    return;
                }

                // Local variables to help determine logic
                var disabled, alpha, beta;

                // Determine logic based on the mode of interest
                // Calculate the previous / next page and if the click actions are allowed
                if (mode === 'prev') {

                    disabled = scope.page - 1 <= 0;
                    var prevPage = scope.page - 1 <= 0 ? 1 : scope.page - 1;

                    if (scope.showFirstLast) {
                        alpha = {
                            value: scope.textFirst,
                            title: scope.textTitleFirst,
                            aClass: scope.textFirstClass,
                            page: 1
                        };
                    }

                    if (scope.showPrevNext) {
                        beta = {
                            value: scope.textPrev,
                            title: scope.textTitlePrev,
                            aClass: scope.textPrevClass,
                            page: prevPage
                        };
                    }

                } else {

                    disabled = scope.page + 1 > pageCount;
                    var nextPage = scope.page + 1 >= pageCount ? pageCount : scope.page + 1;

                    if (scope.showPrevNext) {
                        alpha = {
                            value: scope.textNext,
                            title: scope.textTitleNext,
                            aClass: scope.textNextClass,
                            page: nextPage
                        };
                    }

                    if (scope.showFirstLast) {
                        beta = {
                            value: scope.textLast,
                            title: scope.textTitleLast,
                            aClass: scope.textLastClass,
                            page: pageCount
                        };
                    }

                }

                // Create the Add Item Function
                var buildItem = function (item, disabled) {
                    return {
                        title: item.title,
                        aClass: item.aClass,
                        value: item.aClass ? '' : item.value,
                        liClass: disabled ? scope.disabledClass : '',
                        pgHref: disabled ? '' : scope.pgHref.replace(regex, item.page),
                        action: function () {
                            if (!disabled) {
                                internalAction(scope, item.page);
                            }
                        }
                    };
                };

                // Force disabled if specified
                if (scope.isDisabled) {
                    disabled = true;
                }

                // Add alpha items
                if (alpha) {
                    var alphaItem = buildItem(alpha, disabled);
                    scope.List.push(alphaItem);
                }

                // Add beta items
                if (beta) {
                    var betaItem = buildItem(beta, disabled);
                    scope.List.push(betaItem);
                }
            }

            function addRange(start, finish, scope) {

                // Add our items where i is the page number
                var i = 0;
                for (i = start; i <= finish; i++) {

                    var pgHref = scope.pgHref.replace(regex, i);
                    var liClass = scope.page == i ? scope.activeClass : '';

                    // Handle items that are affected by disabled
                    if (scope.isDisabled) {
                        pgHref = '';
                        liClass = scope.disabledClass;
                    }


                    scope.List.push({
                        value: i,
                        title: scope.textTitlePage.replace(regex, i),
                        liClass: liClass,
                        pgHref: pgHref,
                        action: function () {
                            internalAction(scope, this.value);
                        }
                    });
                }
            }

            function addDots(scope) {
                scope.List.push({
                    value: scope.dots,
                    liClass: scope.disabledClass
                });
            }

            function addFirst(scope, next) {

                addRange(1, 2, scope);

                // We ignore dots if the next value is 3
                // ie: 1 2 [...] 3 4 5 becomes just 1 2 3 4 5
                if (next != 3) {
                    addDots(scope);
                }
            }

            // Add Last Pages
            function addLast(pageCount, scope, prev) {

                // We ignore dots if the previous value is one less that our start range
                // ie: 1 2 3 4 [...] 5 6  becomes just 1 2 3 4 5 6
                if (prev != pageCount - 2) {
                    addDots(scope);
                }

                addRange(pageCount - 1, pageCount, scope);
            }

            function build(scope, attrs) {

                // Block divide by 0 and empty page size
                if (!scope.pageSize || scope.pageSize <= 0) {
                    scope.pageSize = 1;
                }

                // Determine the last page or total page count
                var pageCount = Math.ceil(scope.total / scope.pageSize);

                // Set the default scope values where needed
                setScopeValues(scope, attrs);

                // Validate the scope values to protect against strange states
                validateScopeValues(scope, pageCount);

                // Create the beginning and end page values
                var start, finish;

                // Calculate the full adjacency value
                var fullAdjacentSize = (scope.adjacent * 2) + 2;


                // Add the Next and Previous buttons to our list
                addPrevNext(scope, pageCount, 'prev');

                // If the page count is less than the full adjacnet size
                // Then we simply display all the pages, Otherwise we calculate the proper paging display
                if (pageCount <= (fullAdjacentSize + 2)) {

                    start = 1;
                    addRange(start, pageCount, scope);

                } else {

                    // Determine if we are showing the beginning of the paging list
                    // We know it is the beginning if the page - adjacent is <= 2
                    if (scope.page - scope.adjacent <= 2) {

                        start = 1;
                        finish = 1 + fullAdjacentSize;

                        addRange(start, finish, scope);
                        addLast(pageCount, scope, finish);
                    }

                    // Determine if we are showing the middle of the paging list
                    // We know we are either in the middle or at the end since the beginning is ruled out above
                    // So we simply check if we are not at the end
                    // Again 2 is hard coded as we always display two pages after the dots
                    else if (scope.page < pageCount - (scope.adjacent + 2)) {

                        start = scope.page - scope.adjacent;
                        finish = scope.page + scope.adjacent;

                        addFirst(scope, start);
                        addRange(start, finish, scope);
                        addLast(pageCount, scope, finish);
                    }

                    // If nothing else we conclude we are at the end of the paging list
                    // We know this since we have already ruled out the beginning and middle above
                    else {

                        start = pageCount - fullAdjacentSize;
                        finish = pageCount;

                        addFirst(scope, start);
                        addRange(start, finish, scope);
                    }
                }

                // Add the next and last buttons to our paging list
                addPrevNext(scope, pageCount, 'next');
            }

        });
    </script>

    <!-- inject:style:css -->
    <style>body{margin:0;padding:0;background:#f4f4f4;font-family:sans-serif}.btn{text-decoration:none;background:white;padding:5px 12px;border-radius:25px}header{min-height:30px;display:flex;justify-content:space-between;align-items:center;padding:15px;background:#2e2e2f;position:fixed;left:0;right:0;top:0;box-shadow:0 1px 3px rgba(0,0,0,0.12),0 1px 2px rgba(0,0,0,0.24)}header a{color:#333}header .btn_clear_all{background:#de4f4f;color:#fff}header .name{font-size:25px;font-weight:500;color:white;position:relative}header .name span:nth-child(1){position:absolute;font-size:13px;left:28px;top:-3px}header .name span:nth-child(3){position:absolute;left:28px;top:7px;font-size:22px}.letter_a{background:#FFC107;border-radius:4px;color:#333;padding:0px 3px;margin-right:3px;font-weight:600}.content{margin-top:65px;padding:15px;background:#fff;min-height:100px}.content select,.content input,.content button{box-sizing:border-box;min-height:28px;max-height:28px;min-width:120px;border:1px solid #ddd;border-radius:4px;padding:2px 5px}.content input{padding:2px 7px}.top_content{display:flex;justify-content:space-between;align-items:center;margin-bottom:10px}.top_content .top_content_left{display:flex}.top_content .top_content_left .log_filter{display:flex;align-items:center;margin-left:15px}.top_content .top_content_left .log_filter .log_type_item{margin-right:4px;background:#eae9e9;max-height:20px;font-size:11px;box-sizing:border-box;padding:4px 6px;cursor:pointer}.top_content .top_content_left .log_filter .log_type_item.active{background:#2f2e2f;color:white!important}.top_content .top_content_left .log_filter .log_type_item.clear{background:#607D8B;color:white}.top_content .top_content_right{display:flex}.top_content .top_content_right .user_list_box{position:relative}.top_content .top_content_right .user_list_box #user_list{box-shadow:0 1px 3px rgba(0,0,0,0.12),0 1px 2px rgba(0,0,0,0.24);position:absolute;z-index:10;max-height:200px;border:1px solid #f4f4f4;max-width:100%;min-width:100%;overflow:hidden;box-sizing:border-box;background:white;top:45px;overflow-y:scroll}.top_content .top_content_right .user_list_box #user_list::-webkit-scrollbar{width:3px}.top_content .top_content_right .user_list_box #user_list .single_user{margin-bottom:8px;cursor:pointer;padding:7px}.top_content .top_content_right .user_list_box #user_list .single_user:hover{background:#f4f4f4}.top_content .top_content_right .user_list_box #user_list .single_user p{margin:0}.top_content .top_content_right .user_list_box #user_list .single_user p span{font-size:12px}.top_content .top_content_right .btn_filter{min-width:70px;background:#FFC107;color:#2b2b28;cursor:pointer;border:1px solid #af8300}.top_content .top_content_right .btn_filter_active{background:#F44336;color:#fff;border:1px solid #a51208}.top_content .top_content_right .btn_reset{min-width:70px;cursor:pointer}.top_content .top_content_right .filter_item{margin-right:5px;display:flex;flex-direction:column}.top_content .top_content_right .filter_item label{font-size:13px;margin-bottom:3px;color:#555}.action_column button{min-width:70px;cursor:pointer}.action_column .btn_delete{background:#ef5b50;color:white}.log_data_wrapper{position:relative;min-height:calc(100vh - 205px);overflow-y:auto}.log_data_wrapper::-webkit-scrollbar{width:2px}.log_data_wrapper .loader{position:absolute;z-index:10;top:0;left:0;background:rgba(0,0,0,0.5);width:100%;height:100%;display:flex;align-items:center;justify-content:center;color:#fff}footer{background:#f4f4f4;display:flex;justify-content:space-between;box-sizing:border-box;padding:10px}footer .footer_right .btn{background:#ef5b50;color:white;cursor:pointer}table{border:1px solid #ccc;border-collapse:collapse;margin:0;padding:0;width:100%}table tr{border:1px solid #e8e8e8;padding:5px}table tr:hover{background:#f4f4f4}thead tr td{background:#717171;color:#fff}table th,table td{padding:6px 6px;font-size:15px;color:#666}table th{font-size:14px;letter-spacing:1px;text-transform:uppercase}.lbl_table{margin-top:4px;display:inline-block;margin-left:3px;color:#777}.field_cell{background:#f4f4f4;width:150px}.changed{background:antiquewhite}.edit_badge{width:33px;display:inline-block;text-align:center}@media screen and (max-width:700px){.top_content{flex-direction:column}.top_content .top_content_left{flex-direction:column}.top_content .log_filter{flex-wrap:wrap}.top_content .log_filter .log_type_item{margin-bottom:3px}}.popup_wrapper{position:fixed;top:0;left:0;width:100%;height:100vh;background:rgba(0,0,0,0.5)}.popup{width:50%;background:#fff;position:absolute;margin:0 auto;left:0;right:0;top:20%;z-index:200;box-shadow:1px 0 20px rgba(0,0,0,0.19),0 6px 6px rgba(0,0,0,0.23);max-height:calc(100vh - 35%);overflow-x:hidden;overflow-y:scroll}.popup::-webkit-scrollbar{width:3px}.popup .header{display:flex;background:whitesmoke;justify-content:space-between;height:35px;align-items:center;border-bottom:1px solid #ddd}.popup .header .title{margin-left:15px;font-weight:700}.popup .header .close{width:30px;background:#333;color:#fff;text-align:center;line-height:35px;border-bottom:1px solid #333;cursor:pointer}.popup .popup_content{min-height:100px;width:100%;padding:15px}.popup .popup_content .form-group{margin-bottom:10px}.popup .popup_content label{display:block;color:#777}.popup .popup_content input{border:0;padding:5px;border:1px solid #ddd;width:100%;margin-top:5px}.popup .popup_content input:focus{outline:none;border-bottom:1px solid #666}.popup .footer{padding:0 15px;display:flex;background:whitesmoke;justify-content:space-between;height:40px;align-items:center;border-top:1px solid #ddd}.pagination_wrapper{display:flex;justify-content:flex-end}.pagination_wrapper ul.pagination{display:flex;padding:0;margin-right:5px}.pagination_wrapper ul.pagination li{list-style-type:none}.pagination_wrapper ul.pagination li a{padding:5px 8px;background:#f4f4f4;border:1px solid #ddd;display:inline-block;text-decoration:none;color:#000;margin-right:3px}.pagination_wrapper ul.pagination li a:hover{background:#222;color:#fff}.pagination_wrapper ul.pagination li .active{background:#222;color:#fff}.pagination_wrapper ul.pagination li.active a{background:#222;color:#fff}@media screen and (max-width:600px){.top_content{flex-direction:column-reverse}.top_content_right{flex-wrap:wrap}.top_content_right .filter_item{min-width:48%;margin-bottom:8px}.top_content_right .full_width_param{min-width:98%;max-width:98%}.pagination_wrapper{display:flex;justify-content:flex-start;overflow-x:scroll}.btn{font-size:13px}.dt_box,.selected_date{text-align:center}.responsive_table{max-width:100%;overflow-x:auto}.popup{width:96%!important}.popup .popup_content table{width:93% !important}.popup .popup_content table td{width:96%}table{border:0}table thead{display:none}table tr{border-bottom:2px solid #ddd;display:block;margin-bottom:10px}table td{border-bottom:1px dotted #ccc;display:block;font-size:15px}table td:last-child{border-bottom:0}table td:before{content:attr(data-label);float:left;font-weight:bold;text-transform:uppercase}}.badge{padding:3px 8px;-webkit-border-radius:25px;-moz-border-radius:25px;border-radius:25px;font-size:12px}.badge.primary{background:#4ba4ea;color:white}.badge.info{background:#6bb5b5;color:#fff}.badge.warning{background:#f7be57}.badge.critical{background:#de4f4f;color:#fff}.badge.emergency{background:#ff6060;color:white}.badge.notice{background:bisque}.badge.debug{background:#8e8c8c;color:white}.badge.alert{background:#4ba4ea;color:white}.badge.error{background:#c36a6a;color:white}.text_center{text-align:center}.text_right{text-align:right}.text_left{text-align:left}.text_light{color:#888}.spinner{margin:100px auto 0;width:70px;text-align:center}.spinner>div{width:18px;height:18px;background-color:#fff;border-radius:100%;display:inline-block;-webkit-animation:sk-bouncedelay 1.4s infinite ease-in-out both;animation:sk-bouncedelay 1.4s infinite ease-in-out both}.spinner .bounce1{-webkit-animation-delay:-0.32s;animation-delay:-0.32s}.spinner .bounce2{-webkit-animation-delay:-0.16s;animation-delay:-0.16s}@-webkit-keyframes sk-bouncedelay{0%,80%,100%{-webkit-transform:scale(0)}40%{-webkit-transform:scale(1)}}@keyframes sk-bouncedelay{0%,80%,100%{-webkit-transform:scale(0);transform:scale(0)}40%{-webkit-transform:scale(1);transform:scale(1)}}</style>
    <!-- endinject -->
</head>
<body ng-controller="LogCtrl">
<header>
    <div class="name">
        <span>user</span>
        <span class="letter_a">A</span> <span>ctivity</span>
    </div>
    <div class="actions">
        <a class="btn" href="{{url(config('user-activity.admin_panel_path'))}}">Goto Admin Panel</a>
        <a class="btn" href="https://laravelarticle.com/laravel-user-activity" title="Laravel User Activity">Doc</a>
    </div>
</header>
<section class="content">
    <div class="top_content">
        <div class="top_content_left">
            <p class="text_light">Showing @{{response.from}} to @{{response.to}} of @{{response.total}} records</p>
        </div>

        <div class="top_content_right">
            <div class="filter_item full_width_param user_list_box">
                <label>USER</label>
                <input type="text"
                       ng-model="filter.user_id"
                       ng-model-options="{debounce:500}"
                       ng-change="getUsers(filter.user_id)"
                       placeholder="Type name or id">

                <div id="user_list" ng-show="userList.length && filter.user_id">
                    <!---->
                    <div class="single_user" ng-repeat="user in userList" ng-click="onUserSelect(user)">
                        <p>@{{ user.name }} <br>
                            <span class="text_light">@{{ user.email }}</span>
                        </p>
                    </div>
                    <!---->
                </div>

            </div>
            <div class="filter_item">
                <label>LOG TYPE</label>

                <select ng-model="filter.log_type">
                    @foreach(['create','edit','delete','login','lockout'] as $type)
                        <option value="{{ $type}}">{{ $type}}</option>
                    @endforeach
                </select>
            </div>
            <div class="filter_item">
                <label>TABLE</label>
                <select ng-model="filter.table">
                    @foreach($tables as $table)
                        <option value="{{ $table}}">{{ $table}}</option>
                    @endforeach
                </select>
            </div>
            <div class="filter_item">
                <label>FROM DATE</label>
                <date-input ng-model="filter.from_date"></date-input>
            </div>
            <div class="filter_item">
                <label>TO DATE</label>
                <date-input ng-model="filter.to_date"></date-input>
            </div>
            <div class="filter_item" style="justify-content: flex-end;">
                <button class="btn_reset" ng-show="activeFilter" ng-click="resetParam()">RESET</button>
            </div>
            <div class="filter_item" style="justify-content: flex-end;">
                <button class="btn_filter" ng-class="{btn_filter_active : activeFilter == true}"
                        ng-click="filterData(filter)">FILTER
                </button>
            </div>
        </div>
    </div>

    <div class="log_data_wrapper">
        <div class="loader" ng-show="isLoading">
            <div class="spinner">
                <div class="bounce1"></div>
                <div class="bounce2"></div>
                <div class="bounce3"></div>
            </div>
        </div>
        <div class="responsive_table">
            <table>
                <thead>
                <tr>
                    <td width="30">ID</td>
                    <td width="260">DATE</td>
                    <td width="170">LOG TYPE</td>
                    <td>DONE BY</td>
                    <td class="text_right" style="padding-right: 10px;">ACTION</td>
                </tr>
                </thead>

                <tr ng-repeat="log in data |filter: selectedType track by $index">
                    <td style="border-right: 1px solid #ddd;">@{{ log.id }}</td>
                    <td>
                        @{{ log.log_date }} - @{{log.dateHumanize}}
                    </td>
                    <td ng-switch="log.log_type">
                        <span ng-switch-when="delete|lockout"
                              ng-switch-when-separator="|" class="badge emergency">@{{log.log_type}}</span>

                        <span ng-switch-when="create" class="badge info">@{{log.log_type}}</span>
                        <span ng-switch-when="create" class="lbl_table">to @{{log.table_name}}</span>
                        <span ng-switch-when="edit" class="badge warning edit_badge">@{{log.log_type}}</span>

                        <span ng-switch-when="edit|delete"
                              ng-switch-when-separator="|" class="lbl_table">from @{{ log.table_name }}</span>

                        <span ng-switch-default class="badge debug">@{{log.log_type}}</span>
                    </td>

                    <td>
                        <strong>@{{ log.user.name }}</strong><br>
                        <span class="text_light">@{{ log.user.email }}</span>
                    </td>
                    <td class="action_column text_right">
                        <button class="btn_show" ng-click="showPopup(log)">SHOW</button>
                    </td>
                </tr>
            </table>

            <div class="pagination_wrapper">
                <div paging
                     page="response.current_page"
                     page-size="response.per_page"
                     total="response.total"
                     paging-action="init(page)"
                     scroll-top="true"
                     hide-if-empty="true"
                     show-prev-next="true"
                     show-first-last="true">
                </div>
            </div>

        </div>
    </div>
    <footer>
        <div></div>
        <div class="footer_right">
          <span class="text_light">Delete data older than {{ config('user-activity.delete_limit') }} days</span>
            <button class="btn" ng-click="deleteLog()">DELETE</button>
        </div>
    </footer>

    <div class="popup_wrapper" ng-show="popup">
        <div class="popup" style="width: 60%">
            <div class="header">
                <div class="title">Log Preview</div>
                <div class="close" ng-click="popup=false">x</div>
            </div>
            <div class="popup_content">
                <table style="width: 96%;">
                    <thead>
                    <tr>
                        <td colspan="2">INFO</td>
                    </tr>
                    </thead>
                    <tr>
                        <td class="field_cell">Type</td>
                        <td ng-switch="selected.log_type">
                            <span ng-switch-when="delete|lockout"
                                  ng-switch-when-separator="|" class="badge emergency">@{{selected.log_type}}</span>

                            <span ng-switch-when="create" class="badge info">@{{selected.log_type}}</span>
                            <span ng-switch-when="edit" class="badge warning edit_badge">@{{selected.log_type}}</span>
                            <span ng-switch-default class="badge debug">@{{selected.log_type}}</span>
                        </td>
                    </tr>
                    <tr ng-show="['create','edit','delete'].includes(selected.log_type)">
                        <td class="field_cell">Table</td>
                        <td>@{{ selected.table_name }}</td>
                    </tr>
                    <tr>
                        <td class="field_cell">Time</td>
                        <td>@{{ selected.dateHumanize }} - @{{ selected.log_date }}</td>
                    </tr>
                    <tr>
                        <td class="field_cell">Done by</td>
                        <td>@{{ selected.user.name }} - <span class="text_light">@{{ selected.user.email }}</span></td>
                    </tr>
                </table>


                <br>

                <div class="responsive_table">
                    <table style="width: 96%;">
                        <thead>
                        <tr>
                            <td>@{{ ['edit','delete'].includes(selected.log_type)?'FIELD':'' }}</td>
                            <td>@{{ selected.log_type==='edit'?'PREVIOUS':'DATA' }}</td>
                            <td ng-show="selected.log_type==='edit'">CURRENT</td>
                        </tr>
                        </thead>
                        <tbody>

                        <tr ng-repeat="(field,value) in selected.json_data">
                            <td class="field_cell">@{{ field }}</td>
                            <td>@{{ value }}</td>
                            <td ng-show="selected.log_type==='edit'" ng-class="value!=currentData[field]?'changed':''">
                                @{{ currentData[field] }}
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>

                <br>
                <div class="responsive_table" ng-if="selected.log_type==='edit' && editHistory.length > 0">
                    <p style="color: #666;;">Another <strong>@{{ editHistory.length }}</strong> edit history found!</p>
                    <table style="width: 96%;">
                        <thead>
                        <tr>
                            <td>Time</td>
                            <td>Edit By</td>
                            <td>Data</td>
                        </tr>
                        </thead>
                        <tbody>

                        <tr ng-repeat="h in editHistory">
                            <td>@{{ h.dateHumanize }}</td>
                            <td>@{{ h.user.name }}</td>
                            <td style="overflow: hidden">@{{ h.data }}</td>
                        </tr>
                        </tbody>
                    </table>
                </div>

            </div>
            <div class="footer">
                <div>

                </div>
                <div>

                </div>
            </div>
        </div>
    </div>
    @include('LaravelUserActivity::partials.script')
</section>

</body>
</html>
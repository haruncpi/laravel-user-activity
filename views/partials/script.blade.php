<script>
    var myApp = angular.module("myApp", ["__paging"]);
    myApp.directive(
        'dateInput',
        function (dateFilter) {
            return {
                require: 'ngModel',
                template: '<input type="date" class="form-control"/>',
                replace: true,
                link: function (scope, elm, attrs, ngModelCtrl) {
                    ngModelCtrl.$formatters.unshift(function (modelValue) {
                        return dateFilter(modelValue, 'yyyy-MM-dd');
                    });

                    ngModelCtrl.$parsers.push(function (modelValue) {
                        return moment(modelValue).format('YYYY-MM-DD');
                    })

                }
            };
        });

    myApp.controller("LogCtrl", function ($scope, $http) {

        var ROUTE_PATH = '{{url(config('user-activity.route_path'))}}';
        $scope.selected = {};
        $scope.popup = false;
        $scope.activeFilter = false;
        $scope.isLoading = false;

        $scope.filter = {
            user_id: null,
            log_type: null,
            table: null,
            from_date: null,
            to_date: null
        };


        $scope.showPopup = function (logData) {
            console.log(logData);

            $scope.selected = logData;
            if (logData.log_type === 'edit') {
                $scope.popup = true;
                $scope.getCurrentData(logData)
            } else {
                $scope.popup = true;
            }
        };

        $scope.getCurrentData = function (logData) {
            var param = {
                action: 'current_data',
                table: logData.table_name,
                id: logData.json_data.id,
                log_id: logData.id
            };
            $scope.currentData = {};
            $scope.editHistory = {};
            var url = ROUTE_PATH + objectToQueryString(param);
            $http.get(url)
                .success(function (data) {
                    $scope.currentData = data.current_data;
                    $scope.editHistory = data.edit_history;
                    console.log(data)
                })
        };


        var objectToQueryString = function (oParameters) {
            return "?" + Object.keys(oParameters).map(function (key) {
                if (oParameters[key]) {
                    return key + "=" + encodeURIComponent(oParameters[key]);
                }
            }).filter(function (elem) {
                return !!elem;
            }).join("&");
        };

        $scope.init = function (pageNumber) {
            if (pageNumber === undefined) {
                pageNumber = '1';
            }
            var url = ROUTE_PATH + '?action=data&page=' + pageNumber;

            if ($scope.activeFilter) {
                var param = $scope.filter;
                if (param.from_date !== null && param.to_date == null) {
                    alert('From & To date required');
                    return;
                }
                if (param.to_date !== null && param.from_date == null) {
                    alert('From & To date required');
                    return;
                }

                var query = objectToQueryString(param);
                url = url + query.replace('?', '&');
                console.log(url)
            }

            $scope.isLoading = true;
            $http.get(url).success(function (data) {
                $scope.response = data;
                $scope.data = data.data;
                $scope.isLoading = false;
            })
        };

        $scope.init();

        $scope.resetParam = function () {
            $scope.activeFilter = false;
            $scope.filter = {
                user_id: null,
                log_type: null,
                table: null,
                from_date: null,
                to_date: null
            };
            $scope.init();
        };

        $scope.filterData = function () {
            $scope.activeFilter = true;
            $scope.init();
        };

        $scope.getUsers = function (user) {
            if (!user) return;
            var url = ROUTE_PATH + '?action=user_autocomplete&user=' + user;
            $http.get(url)
                .success(function (data) {
                    $scope.userList = data;
                })
        };

        $scope.onUserSelect = function (user) {
            $scope.filter.user_id = user.id;
            $scope.user_query = user.id;
            $scope.userList.length = 0
        };

        $scope.deleteLog = function () {
            if (!confirm('Are you sure?')) return;
            $http.post(ROUTE_PATH, {action: 'delete'})
                .success(function (data) {
                    if (data.success) {
                        alert(data.message)
                        $scope.init()
                    } else {
                        alert('Something went wrong')
                    }

                })
        }

    })
</script>
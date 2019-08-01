(function(factory) {
    console.log(1);
    var global = typeof window != 'undefined' ? window : this;

    if (typeof module === 'object' && typeof module.exports === 'object') {
        module.exports = global.document ? factory(global) : function(win) {
            if (!win.document) throw new Error("document is a undefined");
            return factory(win);
        };
    } else {
        factory(global);
    }

}

(function(window) {
    console.log(2);
    var relativeMonth = 0,
        // 当前相对月份
        relativeYear = 0,
        // 当前相对年份
        sildeing = false; // 日历列表正在滑动
    var oCalenWrap = create('div', {
            "class": 'calendar'
        },'<header></header>'),
        // 最大父级
        oCalenMask = create('div', {
            "class": 'calendar-mask'
        }),
        // 灰快遮罩
        oCalen = create('div', {
            "class": 'calendar-content'
        }),
        // 日历box
        calendarList = create('div', {
            "class": 'calendar-list'
        }),
        // 日历列表
        calenTitles,
        // 年，月标题
        aMonths,
        // 可以选择的所有月份
        aYears,
        // 可以选择的所有年份
        yearTitle,
        // 当前年标题
        monthTitle,
        // 当前月标题
        selectYearBox,
        // 年份选择
        selectMonthBox; // 月份选择

    function Calendar() {
        console.log(3);
        var date = new Date();
        this.past = false;
        this.hours = false;
        this.hoursPast = false;
        this.currentNode = null;
        this.minDate = null,
        this.maxDate = null,
        this.shield = '[]';
        this.startDate = '';
        this.startJSON = {};
        this.fixDate = {
            y: date.getFullYear(),
            m: date.getMonth() + 1,
            d: 0
        };

        // 开始初始化
        this.init();
    }

    // 初始化
    Calendar.prototype.init = function() {
        console.log(4);
        var self = this;

        var aCalendars = document.querySelectorAll('.calendars');
        if (!aCalendars.length) return;

        oCalenWrap.appendChild(oCalen);
        document.body.appendChild(oCalenWrap);

        // 创建头部
        this.createHeader(function() {
            console.log(5);
            // 创建星期标题头
            this.createWeek();
            var footer=create('div', {
                "class": 'calendar-foot'
            });
            var cancel=create('div', {
                "class": 'foot-cancel'
            },'取消');
            var save=create('div', {
                "class": 'foot-save'
            },'确定');
            
            
            
            oCalen.appendChild(calendarList);
            var div =  '<div class="prompt">已经连续签到<span>3</span>天哦~</div>'+
				'<div class="hear"><img class="left" src="/djptNew/Public/Mob/images/lottery/button.png" /><img class="right" src="/djptNew/Public/Mob/images/lottery/button.png" /></div>'
            $(oCalen).append(div);
            
        	$(oCalen).append('<a href="lottery.html" class="lottery">抽奖看积分</a>');
//          oCalen.appendChild(footer);
            cancel.onclick=hideCalen;
            // 滑动切换上下月
            sildeSwitch(calendarList,
                function(obj, dir) {
                    console.log(6);
                    dir > 0 ? relativeMonth-- : relativeMonth++;

                    this.startJSON.prev.m = relativeMonth - 1;
                    this.startJSON.now.m = relativeMonth;
                    this.startJSON.next.m = relativeMonth + 1;
                    this.transitions(obj, dir);
                }.bind(this));

            // 显示/隐藏 月/年 份选择    点击的年月上绑定事件显示年份或者月份
        //     calenTitles.forEach(function(titleEle) {
        //         titleEle.onclick = function() {
        //             if (this.classList.contains('calendar-month-txt')) {
        //                 // 显示或者隐藏
        //                 selectMonthBox.classList.toggle('active');
        //
        //                 // 同时隐藏年月份
        //                 if (selectYearBox.show) {
        //                     selectYearBox.show = false;
        //                     selectYearBox.classList.remove('active');
        //                 }
        //
        //                 // 设置当前月份高亮
        //                 aMonths.forEach(function(monthEle) {
        //                     attribute(monthEle, 'data-value') === attribute(this, 'data-value') ? monthEle.classList.add('active') : monthEle.classList.remove('active');
        //                 }.bind(this));
        //                 selectMonthBox.show = !selectMonthBox.show;
        //             } else if (this.classList.contains('calendar-year-txt')) {
        //                 selectYearBox.classList.toggle('active');
        //
        //                 if (selectMonthBox.show) {
        //                     selectMonthBox.classList.remove('active');
        //                     selectMonthBox.show = false;
        //                 }
        //
        //                 // 设置当前年份高亮
        //                 aYears.forEach(function(yearEle) {
        //                     var classList = yearEle.classList;
        //
        //                     attribute(yearEle, 'data-value') === attribute(this, 'data-value') ? classList.add('active') : classList.remove('active');
        //                 }.bind(this));
        //                 selectYearBox.show = !selectYearBox.show;
        //             }
        //         }
        //     });
        }.bind(this));

        // 月
        this.createDate({},
            function(months) {
                console.log(7);
                for (var i = 0; i < aMonths.length; i++) {

                    months[i].onclick = function() {
                        console.log(8);
                        for (var x = 0; x < months.length; x++) months[x].classList.remove('active');
                        relativeMonth += attribute(this, 'data-value') - attribute(monthTitle, 'data-value');
                        self.selectDate(this, selectMonthBox, 'm', relativeMonth);
                    }
                }
            });

        // 显示日历
        for (var i = 0; i < aCalendars.length; i++) {

            attribute(aCalendars[i], 'readonly', 'true');
            //window.onload =function focus() {
            //    console.log(this)
            $(document).ready(function(){
                console.log(9);
                var _this=aCalendars[i];
                if (attribute(_this, 'disabled') != null) return;
                var start = Number(attribute(_this, 'start-year')) || 1915;
                var end = Number(attribute(_this, 'end-year')) || 2050;

                self.hours = !(attribute(_this, 'hours') == null);
                self.hoursPast = !(attribute(_this, 'hours-past') == null);

                self.past = !(attribute(_this, 'past') == null) || self.hoursPast;

                self.minDate = getDate(attribute(_this, 'min-date') || '')[0];
                self.maxDate = getDate(attribute(_this, 'max-date') || '')[0];

                self.shield = getDate(attribute(_this, 'shield') || '');
                self.startDate = getDate(attribute(_this, 'start-date') || '');

                var prev, now, next, oDate = new Date();

                if (self.startDate instanceof Array && self.startDate.length) {
                    var startDate = self.startDate[0];

                    relativeYear = startDate.y - oDate.getFullYear();
                    relativeMonth = startDate.m - (oDate.getMonth() + 1);

                    for (var key in startDate) self.fixDate[key] = startDate[key];

                    prev = {
                        y: relativeYear,
                        m: relativeMonth - 1,
                        d: startDate.d
                    };
                    now = {
                        y: relativeYear,
                        m: relativeMonth,
                        d: startDate.d
                    };
                    next = {
                        y: relativeYear,
                        m: relativeMonth + 1,
                        d: startDate.d
                    };

                    self.startJSON = {
                        prev: prev,
                        now: now,
                        next: next
                    };
                } else {
                    self.fixDate.y = oDate.getFullYear();
                    self.fixDate.m = oDate.getMonth() + 1;
                    self.fixDate.d = 0;
                }

                if (self.currentNode != this) {

                    if (!self.startDate instanceof Array || !self.startDate) {
                        relativeYear = relativeMonth = 0;

                        self.startJSON.prev = {
                            y: relativeYear,
                            m: relativeMonth - 1
                        };
                        self.startJSON.now = {
                            y: relativeYear,
                            m: relativeMonth
                        };
                        self.startJSON.next = {
                            y: relativeYear,
                            m: relativeMonth + 1
                        };
                    }

                    // 创建日历对象列表
                    self.appendList(self.startJSON, self.addEvent.bind(self));

                    // 年
                    self.createDate({
                            start: start,
                            end: end,
                            type: 'year'
                        },
                        function(years) {
                            console.log(10);
                            for (var length = 0; length < years.length; length++) {

                                years[length].onclick = function() {
                                    console.log(11);
                                    for (var x = 0; x < years.length; x++) years[x].classList.remove('active');

                                    relativeYear += attribute(this, 'data-value') - attribute(yearTitle, 'data-value');
                                    self.selectDate(this, selectYearBox, "y", relativeYear);
                                }
                            }

                            sildeSwitch(selectYearBox,
                                function(obj, dir) {
                                    console.log(12);
                                    selectYearBox.index = selectYearBox.index || 0;
                                    var count = selectYearBox.children.length;

                                    if (dir > 0) {
                                        selectYearBox.index++;
                                        if (selectYearBox.index >= 0) selectYearBox.index = 0;
                                    } else {
                                        selectYearBox.index--;
                                        if (selectYearBox.index <= -count) selectYearBox.index = -(count - 1);
                                    }

                                    var val = 'translate3D(' + (selectYearBox.index * (100 / count)) + '%, 0, 0)';

                                    selectYearBox.style.WebkitTransform = val;
                                    selectYearBox.style.transform = val;
                                })
                        });
                }

                oCalenWrap.classList.add('active');
                self.currentNode = _this;

            });
            /*Calendars[i].addEventListener('focus',function focus() {
                if (attribute(this, 'disabled') != null) return;
                console.log(this);
                var start = Number(attribute(this, 'start-year')) || 1915;
                var end = Number(attribute(this, 'end-year')) || 2050;

                self.hours = !(attribute(this, 'hours') == null);
                self.hoursPast = !(attribute(this, 'hours-past') == null);

                self.past = !(attribute(this, 'past') == null) || self.hoursPast;

                self.minDate = getDate(attribute(this, 'min-date') || '')[0];
                self.maxDate = getDate(attribute(this, 'max-date') || '')[0];

                self.shield = getDate(attribute(this, 'shield') || '');
                self.startDate = getDate(attribute(this, 'start-date') || '');

                var prev, now, next, oDate = new Date();

                if (self.startDate instanceof Array && self.startDate.length) {
                    var startDate = self.startDate[0];

                    relativeYear = startDate.y - oDate.getFullYear();
                    relativeMonth = startDate.m - (oDate.getMonth() + 1);

                    for (var key in startDate) self.fixDate[key] = startDate[key];

                    prev = {
                        y: relativeYear,
                        m: relativeMonth - 1,
                        d: startDate.d
                    };
                    now = {
                        y: relativeYear,
                        m: relativeMonth,
                        d: startDate.d
                    };
                    next = {
                        y: relativeYear,
                        m: relativeMonth + 1,
                        d: startDate.d
                    };

                    self.startJSON = {
                        prev: prev,
                        now: now,
                        next: next
                    };
                } else {
                    self.fixDate.y = oDate.getFullYear();
                    self.fixDate.m = oDate.getMonth() + 1;
                    self.fixDate.d = 0;
                }

                if (self.currentNode != this) {

                    if (!self.startDate instanceof Array || !self.startDate) {
                        relativeYear = relativeMonth = 0;

                        self.startJSON.prev = {
                            y: relativeYear,
                            m: relativeMonth - 1
                        };
                        self.startJSON.now = {
                            y: relativeYear,
                            m: relativeMonth
                        };
                        self.startJSON.next = {
                            y: relativeYear,
                            m: relativeMonth + 1
                        };
                    }

                    // 创建日历对象列表
                    self.appendList(self.startJSON, self.addEvent.bind(self));

                    // 年
                    self.createDate({
                            start: start,
                            end: end,
                            type: 'year'
                        },
                        function(years) {
                            for (var length = 0; length < years.length; length++) {

                                years[length].onclick = function() {
                                    alert("点击了");
                                    for (var x = 0; x < years.length; x++) years[x].classList.remove('active');

                                    relativeYear += attribute(this, 'data-value') - attribute(yearTitle, 'data-value');
                                    self.selectDate(this, selectYearBox, "y", relativeYear);
                                }
                            }

                            sildeSwitch(selectYearBox,
                                function(obj, dir) {
                                    selectYearBox.index = selectYearBox.index || 0;
                                    var count = selectYearBox.children.length;

                                    if (dir > 0) {
                                        selectYearBox.index++;
                                        if (selectYearBox.index >= 0) selectYearBox.index = 0;
                                    } else {
                                        selectYearBox.index--;
                                        if (selectYearBox.index <= -count) selectYearBox.index = -(count - 1);
                                    }

                                    var val = 'translate3D(' + (selectYearBox.index * (100 / count)) + '%, 0, 0)';

                                    selectYearBox.style.WebkitTransform = val;
                                    selectYearBox.style.transform = val;
                                })
                        });
                }

                oCalenWrap.classList.add('active');
                self.currentNode = this;
            });*/
        }
        oCalen.onclick = function(ev) {
            console.log(14);
            var oEv = ev.targetTouches ? ev.targetTouches[0] : (ev || event);
            oEv.cancelBubble = true;
        }

        oCalenMask.onclick = hideCalen;
    }

    /**
     * 创建日历列表
     * @return {[type]}        [description]
     */
    Calendar.prototype.createCalenList = function(data, setTitle) {
        console.log(15);
        var self = this;

        var oList = document.createElement('div');
        var createdCount = 0;
        var mixinYear = data.y || 0;
        var mixinMonth = data.m || 0;
        var mixinDay = data.d;
        var minDate = this.minDate || {};
        var maxDate = this.maxDate || {};

        var date = new Date();

        date.setFullYear(date.getFullYear() + mixinYear, (date.getMonth() + mixinMonth + 1), 1);
        date.setDate(0);

        var dSun = date.getDate();
        date.setDate(1);

        var dWeek = date.getDay();

        var date = new Date();
        var today = date.getDate();

        date.setFullYear(date.getFullYear() + mixinYear, date.getMonth() + mixinMonth, 1);

        // 获取当前年月
        var currentYear = date.getFullYear();
        var currentMonth = date.getMonth() + 1;
        alert("currentYear = " + currentYear + "    currentMonth = " + currentMonth);


        // 设置上一个月的最后一天
        date.setDate(0);

        var lastDay = date.getDate();
        var lastMonths = [];

        for (var i = lastDay; i > 0; i--) {
            lastMonths.push(i);
        }

        // 设置标题
        if (setTitle) {
            yearTitle.innerHTML = currentYear;
            monthTitle.innerHTML = (currentMonth < 10 ? '0' + currentMonth : currentMonth);

            attribute(yearTitle, 'data-value', currentYear);
            attribute(monthTitle, 'data-value', currentMonth - 1);
        }

        // 创建上月尾部分
        var lastMonthDay = dWeek + 7;
        lastMonthDay = lastMonthDay >= 10 ? lastMonthDay - 7 : lastMonthDay;

        for (var i = 0; i < lastMonthDay; i++) {
            var spanEle = create('span'),
                dayEle = create('a', {
                        "data-calen": [currentYear, currentMonth - 1, lastMonths[i]].join('/'),
                        "class": 'prev-m prev-to-month pasted',
                        "href": 'javascript:;'
                    },
                    lastMonths[i]);

            spanEle.appendChild(dayEle);

            if (oList.children.length !== 0) {
                oList.insertBefore(spanEle, oList.children[0]);
            } else {
                oList.appendChild(spanEle);
            }

            createdCount++;
        }

        var nowTime = getTime(currentYear, currentMonth, today);
        var minDateTime = getTime(minDate.y, minDate.m, minDate.d);
        var maxDateTime = getTime(maxDate.y, maxDate.m, maxDate.d);

        // 这当前月的日期列表
        for (var i = 0; i < dSun; i++) {
            createdCount++;
			
            var day = i + 1;
            var spanEle = create('span');
            var dayEle = create('a', {
                    "data-calen": [currentYear, currentMonth, day].join('/'),
                    "href": 'javascript:;'
                },
                day);
            var oDate = new Date();
			
			//自定义函数，为以前的日期添加星星
			// xinxin(dayEle);
			
            // 设置周末的样式
            if (createdCount % 7 === 0 || createdCount % 7 === 1) {
                dayEle.classList.add('weekend');
            }

            var time = getTime(mixinYear + currentYear, mixinMonth + currentMonth, day);
            var contrastTime = getTime(currentYear, currentMonth, day)

            // 设置样式
            if (
                self.past && time < nowTime ||
                minDateTime && contrastTime < minDateTime ||
                maxDateTime && contrastTime > maxDateTime
            ) {
                dayEle.classList.add('expire', 'pasted');
            }

            if (
                time === nowTime || 
                self.fixDate.y === currentYear && 
                self.fixDate.m === currentMonth && 
                self.fixDate.d === day
            ) {
                dayEle.classList.add('today');
            }

            // 设置禁用日期
            if (setShiled(currentYear, currentMonth, day)) {
                dayEle.classList.add('pasted', 'shield');
            }

            spanEle.appendChild(dayEle);
            oList.appendChild(spanEle);
        }

        // 创建下月尾部分
        var nextMonths = 42 - oList.children.length;

        for (var i = 0; i < nextMonths; i++) {
            var day = i + 1;
            var spanEle = create('span');
            var dayEle = create('a', {
                    "data-calen": [currentYear, currentMonth + 1, day].join('/'),
                    "class": 'next-m next-to-month pasted',
                    "href": 'javascript:;'
                },
                day);

            spanEle.appendChild(dayEle);
            oList.appendChild(spanEle);
        }

        // 设置禁用日期
        function setShiled(iyear, imonth, idate) {
            console.log(16);
            if (!self.shield) return false;

            for (var index = 0; index < self.shield.length; index++) {
                self.shield[index].y = self.shield[index].y || date.getFullYear();
                self.shield[index].m = self.shield[index].m || date.getMonth() + 1;
                self.shield[index].d = self.shield[index].d || date.getDate();

                if (iyear == self.shield[index].y && imonth == self.shield[index].m && idate == self.shield[index].d) return true;
            }
            return false;
        }

        return oList;
    }

    /**
     * 创建年月
     * @param  {[type]} data.start  [开始日期]
     * @param  {[type]} data.end    [结束日期]
     * @param  {[type]} data.type   ["year"/"month"(默认)]
     * @param  {[type]} callback    [description]
     * @return {[type]}             [description]
     */
    Calendar.prototype.createDate = function(data, callback) {
        console.log(17);
        data = data || {};
        data.start = data.start || 1;
        data.end = data.end || 12;
        data.type = data.type || 'month';

        var oDateList = create('div', {
            "class": (data.type == 'month' ? 'calendar-months' : 'calendar-years')
        });

        var oList = create('div');
        var nodes = [];
        var count = 0;
        var length = 0;
        var now = 0;
        var nowY = (new Date()).getFullYear();

        for (var i = data.start; i <= data.end; i++) {

            var spanEle = create('span');
            var dateEle = create('a', {
                    "data-value": data.type == 'year' ? i : i - 1,
                    "href": 'javascript:;'
                },
                (i < 10 ? '0' + i : i));

            nodes.push(dateEle);

            if (data.type == 'year') {

                if (count >= 12) {
                    oDateList.appendChild(oList);
                    oList = create('div');
                    count = 0;
                    length++;
                }

                if (i == nowY) now = length;

                spanEle.appendChild(dateEle);
                oList.appendChild(spanEle);
            } else {
                spanEle.appendChild(dateEle);
                oDateList.appendChild(spanEle);
            }
            count++;
        };

        if (data.type == 'year') {
            if (selectYearBox && oCalen) oCalen.removeChild(selectYearBox);
            oDateList.appendChild(oList);
            selectYearBox = oDateList;
            aYears = nodes;

            if (count) length++;

            oDateList.style.width = (length * 100) + '%';

            for (var i = 0; i < length; i++) {
                oDateList.children[i].style.width = 100 / length + '%';
            }

            // 设置当前显示的页
            oDateList.style.WebkitTransform = 'translate3D(-' + (now * (100 / length)) + '%, 0, 0)';
            oDateList.style.transform = 'translate3D(-' + (now * (100 / length)) + '%, 0, 0)';
            selectYearBox.index = -now;
        } else {
            if (selectMonthBox && oCalen) oCalen.removeChild(selectMonthBox);
            selectMonthBox = oDateList;
            aMonths = nodes;
        }
        oCalen.appendChild(oDateList);

        callback && callback(nodes);
    }

    /**
     * 创建时间
     * @return {[type]} [description]
     */
    Calendar.prototype.createTime = function(currentNode, date, today) {
        console.log(18);
        var timeList = getElement(oCalen, '.calendar-time');
        var child = [];
        var oDate = new Date();
        var day = oDate.getDate();
        var hours = oDate.getHours();
        var self = this;

        if (!timeList.length) {
            timeList = create('div', {
                "class": 'calendar-time'
            });

            for (var i = 0; i < 24; i++) {

                var time = i < 10 ? '0' + i : i;
                time += ':00';

                var spanEle = create('span');
                var timeEle = create('a', {
                        "href": 'javascript:;',
                        "data-time": time
                    },
                    time);

                spanEle.appendChild(timeEle);
                timeList.appendChild(spanEle);
                child.push({
                    obj: timeEle,
                    time: parseInt(time, 10)
                });
            }
        } else {
            timeList = timeList[0];
            var aEleList = getElement(timeList, 'a');

            for (var i = 0; i < aEleList.length; i++) {
                child.push({
                    obj: aEleList[i],
                    time: parseInt(attribute(aEleList[i], 'data-time'), 10)
                });
            }
        }

        timeList.classList.add('active');

        child.forEach(function(node) {
            console.log(19);
            if (self.hoursPast && ((relativeMonth < 0 && relativeYear <= 0) || (today == day && node.time <= hours) || (relativeMonth <= 0 && relativeYear <= 0 && today < day))) {
                node.obj.classList.add('expire', 'pasted');
                node.obj.active = false;
            } else {
                node.obj.classList.remove('expire', 'pasted');
                node.obj.active = true;
            }

            // 设置日期时间
            node.obj.onclick = function() {
                console.log(20);
                if (this.active) {
                    var val = date + ' ' + (node.time < 10 ? '0' + node.time : node.time) + ':00';

                    if (currentNode.value !== null) {
                        currentNode.value = val;
                    } else if (currentNode.innerHTML != null) {
                        currentNode.innerHTML = val;
                    }
                    hideCalen();
                    self.changes();
                }
                timeList.classList.remove('active');
            }
        });

        oCalen.appendChild(timeList);
    }

    /**
     * 创建头部
     * @return {[type]}      [description]
     */
    Calendar.prototype.createHeader = function(cb) {
        console.log(21);
        calenTitles = calenTitles || [];

        var self = this;
        var header = create('div', {
            "class": 'calendar-header'
        });
        var head=create('div', {
            "class": 'calendar-head'
        },'请选择日期');
        var year = create('div', {
                "class": 'calendar-year'
            }),
            prevYear = create('a', {
                    "class": 'year-prev switch-btn',
                    "href": 'javascript:;'
                },
                '&lt;'),
            nextYear = create('a', {
                    "class": 'year-next switch-btn',
                    "href": 'javascript:;'
                },
                '&gt;'),
            calenYearTxt = create('a', {
                "class": 'calendar-year-txt calendar-title',
                "href": 'javascript:;'
            });

        year.appendChild(prevYear);
        year.appendChild(calenYearTxt);
        year.appendChild(nextYear);

        var month = create('div', {
                "class": 'calendar-month'
            }),
            prevMonth = create('a', {
                    "class": 'month-prev switch-btn',
                    "href": 'javascript:;'
                },
                '<img src="/djptNew/Public/Mob/images/lottery/return.png" />'),
            nextMonth = create('a', {
                    "class": 'month-next switch-btn',
                    "href": 'javascript:;'
                },
                '<img src="/djptNew/Public/Mob/images/lottery/return.png" />'),
            calenMonthTxt = create('a', {
                "class": 'calendar-month-txt calendar-title',
                "href": 'javascript:;'
            });
        
        var div = $('<div class="nain"></div>');
        div.append(calenYearTxt);
        div.append(calenMonthTxt);
        month.appendChild(prevMonth);
        $(month).append(div);
        month.appendChild(nextMonth);


//      header.appendChild(head);
//      header.appendChild(year);
        header.appendChild(month);

        calenTitles.push(calenYearTxt, calenMonthTxt);

        monthTitle = calenMonthTxt;
        yearTitle = calenYearTxt;

        // 按钮切换上下月/年
        prevMonth.onclick = function() {
            console.log(22);
            console.log("prev month onclick");
            self.switchDate(-1);
        };
        nextMonth.onclick = function() {
            console.log(23);
            console.log("prev month onclick");
            self.switchDate(1);
        }
        // prevYear.onclick = function() {
        //     self.switchDate(-1, 'year');
        // }
        // nextYear.onclick = function() {
        //     self.switchDate(1, 'year');
        // }

        if (oCalen.children.length) {
            oCalen.insertBefore(header, oCalen.children[0]);
        } else {
            oCalen.appendChild(header);
        }
        for (var i = 0; i < header.children.length; i++) {
            header.children[i].ontouchstart = function() {
                console.log(24);
                this.classList.add('active');
            }
            header.children[i].ontouchend = function() {
                console.log(25);
                this.classList.remove('active');
            }
        }

        cb && cb();
    }

    /**
     * 创建头部
     * @return {[type]}      [description]
     */
    Calendar.prototype.createWeek = function() {
        console.log(26);
        var week = create('div', {
                "class": 'calendar-week'
            }),
            weeks = '日一二三四五六';

        for (var i = 0; i < 7; i++) {
            var n = i + 1,
                data = {};
            if (n % 7 == 1 || n % 7 == 0) data["class"] = 'weekend';

            week.appendChild(create('span', data, weeks.charAt(i)));
        }
        var qiandao = '<div class="qiandao"><a class="qian" onclick='+'qiandao()'+'><img src="/djptNew/Public/Mob/images/lottery/qiandao.png" /><span>签到</span><span class="yes hide">已签到</span></a></div>'
        
        $(oCalen).append(qiandao);
        oCalen.appendChild(week);
    }

    /**
     *
     * 插入日历对象
     * @param  {Function} callback  [description]
     * @return {[type]}             [description]
     */
    Calendar.prototype.appendList = function(data, callback) {
        console.log(27);
        data = data || {};
        data.prev = data.prev || {
            m: relativeMonth - 1,
            y: relativeYear
        };
        data.now = data.now || {
            m: relativeMonth,
            y: relativeYear
        };
        data.next = data.next || {
            m: relativeMonth + 1,
            y: relativeYear
        };

        calendarList.innerHTML = '';

        calendarList.appendChild(this.createCalenList(data.prev));
        calendarList.appendChild(this.createCalenList(data.now, true));
        calendarList.appendChild(this.createCalenList(data.next));

        callback && callback();
    }

    /**
     * 设置日历事件(提交选中的日期并隐藏日历)
     */
    Calendar.prototype.addEvent = function() {
        console.log(28);
        var self = this;
        Array.prototype.forEach.call(calendarList.querySelectorAll('a'), function (node) {
            node.onclick = function() {
                console.log(29);
                $(".calendar-list .today").removeClass("today");
                $(this).addClass("today");
                var classList = this.classList;
                if (classList.contains('prev-to-month')) {
                    self.switchDate(-1);
                } else if (classList.contains('next-to-month')) {
                    self.switchDate(1);
                } else if (!classList.contains('pasted') && !classList.contains('shield')) {

                    var dateValue = attribute(this, 'data-calen');
                    var date = format(dateValue, attribute(self.currentNode, 'format') || false);
                    if (self.hours) {
                        var today = this.innerHTML;
                        self.createTime(self.currentNode, date, today);
                    } else {
                        if (self.currentNode) {
                            $(".foot-save").click(function(){
                                console.log(30);
                                if($($(self)[0].currentNode).attr("class").indexOf("star")!=-1){
                                    $(".star").val(date);
                                    $("#star").text(date);
                                    $("#time").removeClass("hide");
                                }else{
                                    $(".end").val(date);
                                    $("#end").text(date);
                                    $("#time").removeClass("hide");
                                };
                                hideCalen();
                            });
                        }
                    }
                }
            }
        });
    }

    /**
     * 切换上下月
     * @param  {[type]} dir  [description]
     * @param  {[type]} type [description]
     * @return {[type]}      [description]
     */
    Calendar.prototype.switchDate = function(dir, type) {
        console.log(31);
        var self = this;
        type = type || 'month';

        switch (type) {
            case 'month':
                dir > 0 ? relativeMonth++ : relativeMonth--;

                self.startJSON.prev.m = relativeMonth - 1;
                self.startJSON.now.m = relativeMonth;
                self.startJSON.next.m = relativeMonth + 1;

                self.transitions(calendarList, dir > 0 ? -1 : 1);
                break;
            case 'year':
                self.appendList({
                        prev: {
                            m: relativeMonth,
                            y: relativeYear - 1
                        },
                        next: {
                            m: relativeMonth,
                            y: relativeYear + 1
                        }
                    },
                    function() {
                        console.log(32);
                        dir > 0 ? relativeYear++ : relativeYear--;
                        self.startJSON.prev.y = relativeYear;
                        self.startJSON.now.y = relativeYear;
                        self.startJSON.next.y = relativeYear;
                        self.transitions(calendarList, dir > 0 ? -1 : 1);
                    });
                break;
        }
    }

    /**
     * 切换月份动画
     * @param  {[type]} obj [description]
     * @param  {[type]} dir [上个月还是下个月]
     */
    Calendar.prototype.transitions = function(obj, dir) {
        console.log(33);
        obj.classList.add('silde', dir > 0 ? 'prev-to' : 'next-to');

        setTimeout(function end() {
            console.log(34);
            this.appendList(this.startJSON,
                function() {
                    console.log(35);
                    obj.classList.remove('silde', 'prev-to', 'next-to');
                    this.addEvent();
                }.bind(this));
        }.bind(this), 500);
    }

    /**/
    Calendar.prototype.selectDate = function(obj, obj2, attr, val) {
        console.log(36);
        var self = this;

        this.startJSON.prev[attr] = (attr == 'm' ? val - 1 : val);
        this.startJSON.now[attr] = val;
        this.startJSON.next[attr] = (attr == 'm' ? val + 1 : val);

        this.appendList(this.startJSON, self.addEvent.bind(this));

        obj.classList.add('active');
        obj2.classList.remove('active');

        selectYearBox.show = false;
        selectMonthBox.show = false;
    }

    // 触发元素的change事件
    Calendar.prototype.changes = function() {
        console.log(38);
        var jQuery = (window.jQuery || window.$) || null;

        if (jQuery) {
            if (jQuery(this.currentNode) && jQuery(this.currentNode).change) {
                jQuery(this.currentNode).change();
            }
        } else {
            this.currentNode.onchange && this.currentNode.onchange();
        }
    }
    /**
     * 滑动切换日期
     * @param  {[type]} ev [description]
     * @return {[type]}    [description]
     */
    function sildeSwitch(element, callBack) {
        console.log(39);
        element.onmousedown = start;
        element.addEventListener('touchstart', start, false);

        function start(evt) {
            console.log(40);
            var oEv = evt.targetTouches ? evt.targetTouches[0] : (evt || event);
            var disX = oEv.pageX;
            var needW = parseInt(document.documentElement.clientWidth / 5, 10);
            var dir;

            var self = this;

            function move(evt) {
                console.log(41);
                if (sildeing) return false;

                var oEv = evt.targetTouches ? evt.targetTouches[0] : (evt || event);
                dir = oEv.pageX - disX;

                if (Math.abs(dir) >= needW) {
                    sildeing = true;

                    callBack && callBack(self, dir);
                }

                oEv.preventDefault && oEv.preventDefault();
                return false;
            }

            function end() {
                console.log(42);
                this.onmousemove && (this.onmousemove = null);
                this.onmouseup && (this.onmouseup = null);

                this.removeEventListener('touchmove', move, false);
                this.removeEventListener('touchend', end, false);

                sildeing = false;
            }

            this.onmousemove = move;
            this.onmouseup = end;

            element.addEventListener('touchmove', move, false);
            element.addEventListener('touchend', end, false);
        }
    }

    /**
     * 获取时间戳
     */
    function getTime(year, month, date) {
        console.log(43);
        if (year === undefined || month === undefined || date === undefined) return null;

        return (new Date(year, month, date, 23, 59, 59)).getTime();
    }

    /**
     * 获取元素
     * @param  {[type]} parent [description]
     * @param  {[type]} str    [type]
     */
    function getElement(parent, selector) {
        console.log(44);
        switch (selector.charAt(0)) {
            case '#':
                return parent.querySelector(selector);
            case '.':
                return parent.querySelectorAll(selector);
            default:
                return parent.querySelectorAll(selector);
        }
    }

    /**
     * 创建元素
     * @param  {[type]} tagname [标签名字]
     * @param  {[type]} attr    [属性(多个)]
     * @param  {[type]} html    [内容]
     */
    function create(tagName, attr, html) {
        console.log(45);
        if (!tagName) return;

        attr = attr || {};
        html = html || '';

        var element = document.createElement(tagName);

        for (var i in attr) {
            element.setAttribute(i, attr[i]);
        }

        element.innerHTML = html;
        return element;
    }
    /**
     * 隐藏日历
     */
    function hideCalen() {
        console.log(46);
        oCalenWrap.classList.add('close');
        setTimeout(function() {
                console.log(47);
                oCalenWrap.classList.remove('active', 'close');
            },
            290);
    }

    /**
     * 日历的格式
     */
    function format(dateStr, fmat) {
        console.log(48);
        if (!dateStr) return false;

        var defaultFmat = fmat || 'y/m/d';
        var dateNumber = dateStr.split('/');
        var symbol = defaultFmat.match(/[^a-z\d]/i)[0];
        var dateHash = {
            y: dateNumber[0],
            m: seats(dateNumber[1]),
            d: seats(dateNumber[2])
        };

        return defaultFmat.split(/[^a-z]/i).map(function(mark) {
            console.log(49);
            return dateHash[mark.charAt(0).toLocaleLowerCase()];
        }).join(symbol);
    }

    /**
     * 通过字符串获取年月日
     */
    function getDate(str) {
        console.log(50);
        if (!str) return [];

        var dateList = [];

        if (/^\[|\]$/.test(str)) {
            dateList = JSON.parse(str.replace(/\'/g, '"'));
        } else if (/^\d+[\/-]\d+[\/-]\d+$/.test(str)) {
            dateList = [str];
        }

        return dateList.map(function(dateString) {
            console.log(51);
            var date = new Date(dateString + ' 23:59:59');
            return {
                y: date.getFullYear(),
                m: date.getMonth() + 1,
                d: date.getDate()
            }
        });
    }

    function seats(n) {
        console.log(52);
        return Number(n) < 10 ? '0' + n : n;
    }

    /**
     * 操作对象属性
     */
    function attribute(obj, attr, val) {
        console.log(53);
        switch (arguments.length) {
            case 3:
                obj.setAttribute(attr, val);
                break;
            case 2:
                return obj.getAttribute(attr);
                break;
        }
    }

    window.addEventListener('load',
        function() {
            console.log(54);
            new Calendar();
        },
        false);

    return Calendar;
}));
const MONTH_NAMES = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];
	const DAYS = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];

	function app() {
		return {
			showDatepicker: false,
			dateFromYmd: '',
			dateToYmd: '',
			dateFromValue: '',
			dateToValue: '',
			currentDate: null,
			dateFrom: null,
			dateTo: null,
			endToShow: '',
			month: '',
			year: '',
			no_of_days: [],
			blankdays: [],
			dateFromToValue: '',
			
			convertFromYmd(dateYmd) {
				const year = Number(dateYmd.substr(0, 4));
				const month = Number(dateYmd.substr(5, 2)) - 1;
				const date = Number(dateYmd.substr(8, 2));
				
				return new Date(year, month, date);
			},
			
			convertToYmd(dateObject) {
				const year = dateObject.getFullYear();
				const month = dateObject.getMonth() + 1;
				const date = dateObject.getDate();
				return year + "-" + ('0' + month).slice(-2) + "-" +  ('0' + date).slice(-2);
			},

			initDate() {
				if ( ! this.dateFrom ) {
					if ( this.dateFromYmd ) {
						this.dateFrom = this.convertFromYmd( this.dateFromYmd );
					}
				}
				if ( ! this.dateTo ) {
					if ( this.dateToYmd ) {
						this.dateTo = this.convertFromYmd( this.dateToYmd );
					}
				}
				if ( ! this.dateFrom ) {
					this.dateFrom = this.dateTo;
				}
				if ( ! this.dateTo ) {
					this.dateTo = this.dateFrom;
				}
				if ( this.endToShow === 'from' && this.dateFrom ) {
					this.currentDate = this.dateFrom;
				} else if ( this.endToShow === 'to' && this.dateTo ) {
					this.currentDate = this.dateTo;
				} else {
					this.currentDate = new Date();
				}
				currentMonth = this.currentDate.getMonth();
				currentYear = this.currentDate.getFullYear();
				if ( this.month !== currentMonth || this.year !== currentYear ) {
					this.month = currentMonth;
					this.year = currentYear;
					this.getNoOfDays();
				}
				this.setDateValues();
			},

			isToday(date) {
				const today = new Date();
				const d = new Date(this.year, this.month, date);

				return today.toDateString() === d.toDateString();
			},

			isDateFrom(date) {
				const d = new Date(this.year, this.month, date);

				return d.toDateString() === this.dateFromValue;
			},

			isDateTo(date) {
				const d = new Date(this.year, this.month, date);

				return d.toDateString() === this.dateToValue;
			},

			isInRange(date) {
				const d = new Date(this.year, this.month, date);

				return d > this.dateFrom && d < this.dateTo;
			},
		  
			isDisabled(date) {
				const d = new Date(this.year, this.month, date);

				if ( this.endToShow === 'from' && this.dateTo && d > this.dateTo ) {
					return true;
				}
				if ( this.endToShow === 'to' && this.dateFrom && d < this.dateFrom ) {
					return true;
				}

				return false;
			},
			
			setDateValues() {
				var from = "";
				var to = "";
				if (this.dateFrom) {
					var year = this.dateFrom.getFullYear();
					var month = this.dateFrom.getMonth()+1;
					var day = this.dateFrom.getDate();
					this.dateFromValue = day+"/"+month+"/"+year;
					from = day+"/"+month+"/"+year;
					this.dateFromYmd = this.convertToYmd(this.dateFrom);
				}
				if (this.dateTo) {
					var year2 = this.dateTo.getFullYear();
					var month2 = this.dateTo.getMonth()+1;
					var day2 = this.dateTo.getDate();
					this.dateToValue = day2+"/"+month2+"/"+year2;
					to = day2+"/"+month2+"/"+year2;
					this.dateToYmd = this.convertToYmd(this.dateTo);
				}

			},

			getDateValue(date) {
				let selectedDate = new Date(this.year, this.month, date);
				if ( this.endToShow === 'from' && ( ! this.dateTo || selectedDate <= this.dateTo ) ) {
					this.dateFrom = selectedDate;
					if ( ! this.dateTo ) {
						this.dateTo = selectedDate;
					}
				} else if ( this.endToShow === 'to' && ( ! this.dateFrom || selectedDate >= this.dateFrom ) ) {
					this.dateTo = selectedDate;
					if ( ! this.dateFrom ) {
						this.dateFrom = selectedDate;
					}
				}
				this.setDateValues();
				this.closeDatepicker();
			},

			getNoOfDays() {
				let daysInMonth = new Date(this.year, this.month + 1, 0).getDate();

				let dayOfWeek = new Date(this.year, this.month).getDay();
				let blankdaysArray = [];
				for ( var i=1; i <= dayOfWeek; i++) {
					blankdaysArray.push(i);
				}

				let daysArray = [];
				for ( var i=1; i <= daysInMonth; i++) {
					daysArray.push(i);
				}

				this.blankdays = blankdaysArray;
				this.no_of_days = daysArray;
			},
			
			closeDatepicker() {
				this.endToShow = '';
				this.showDatepicker = false;
			}
		}
	}
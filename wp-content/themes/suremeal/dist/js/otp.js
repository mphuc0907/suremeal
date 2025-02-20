function timer() {  
  return {  
    expiry: new Date().getTime() + 119000, // Set expiry time to 1 minute and 59 seconds from now  
    remaining: null,  
    init() {  
      this.setRemaining();  
      setInterval(() => {  
        this.setRemaining();  
      }, 1000);  
    },  
    setRemaining() {  
      const currentTime = new Date().getTime();  
      const diff = this.expiry - currentTime;  
      this.remaining = Math.max(0, Math.ceil(diff / 1000)); // Calculate remaining time in seconds and round up  
    },  
    minutes() {  
      return {  
        value: Math.floor(this.remaining / 60),  
        remaining: this.remaining % 60  
      };  
    },  
    seconds() {  
      return {  
        value: this.remaining,  
      };  
    },  
    format(value) {  
      return ("0" + parseInt(value)).slice(-2)  
    },  
    time() {  
      return {  
        minutes: this.format(this.minutes().value),  
        seconds: this.format(this.minutes().remaining),  
      }  
    },  
  };  
}  
  
const countdown = timer();  
countdown.init();  
  
// To display the countdown in the console every second  
setInterval(() => {  
  const { minutes, seconds } = countdown.time();  
}, 1000);
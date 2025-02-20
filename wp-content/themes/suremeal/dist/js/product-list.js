function gallery(){
    this.index=0;
    this.load=function(){
        this.rootEl = document.querySelector(".gallery");
        this.platform = this.rootEl.querySelector(".platform");
        this.frames = this.platform.querySelectorAll(".each-frame");
        this.contentArea = this.rootEl.querySelector(".content-area");      
        this.width = this.platform.offsetWidth;
        this.limit = {start:0,end:this.frames.length-1};
        this.frames.forEach(each=>{each.style.width=this.width+"px";});   
        this.goto(this.index);      
      }
      this.set_title = function(){this.rootEl.querySelector(".heading").innerText="Recently viewed products";}
      this.next = function(){this.platform.style.right=this.width * ++this.index + "px";this.set_title();}
      this.prev = function(){this.platform.style.right=this.width * --this.index + "px";this.set_title();}
      this.goto = function(index){this.platform.style.right = this.width * index + "px";this.index=index;this.set_title();}
      this.load();
  }
  var G = new gallery();
    G.rootEl.addEventListener("click",function(t){
        var val = t.target.getAttribute("action");
        if(val == "next" && G.index != G.limit.end){G.next();}
        if(val == "prev" && G.index != G.limit.start){G.prev();}
        if(val == "goto"){
            let rv = t.target.getAttribute("goto");
            rv = rv == "end" ? G.limit.end:rv;
            G.goto(parseInt(rv));
        }
    });
    document.addEventListener("keyup",function(t){
        var val = t.keyCode;
        if(val == 39 && G.index != G.limit.end){G.next();}
        if(val == 37 && G.index != G.limit.start){G.prev();}
    });

function range() {
	 return {
	       minprice: 40, 
	       maxprice: 200,
	       min: 1, 
	       max: 300,
	       minthumb: 0,
           minthumbcus: 0,
	       maxthumb: 0, 
           maxthumbcus: 0, 
	   
	       mintrigger() {   
    	     this.minprice = Math.min(this.minprice, this.maxprice - 1);      
    	     this.minthumb = ((this.minprice - this.min) / (this.max - this.min)) * 100;
             this.minthumbcus = this.minthumb - 5;
             
	       },
	    
    	    maxtrigger() {
    	     this.maxprice = Math.max(this.maxprice, this.minprice + 1); 
    	     this.maxthumb = 100 - (((this.maxprice - this.min) / (this.max - this.min)) * 100);    
             this.maxthumbcus = this.maxthumb - 11;
    	    }, 


        tooltipTop: 0,  
        tooltipLeft: 0,  

        showTooltip(event) {  
            this.tooltipTop = (event.clientY + window.scrollY + 10) + 'px';  
            this.tooltipLeft = (event.clientX + window.scrollX + 10) + 'px';
        },  
	 }
}

function backToTop() {  
    return {  
        show: true,  
        handleScroll() {  
            this.show = (window.scrollY > 200);
        },  
        scrollToTop() {  
            window.scrollTo({ top: 0, behavior: 'smooth' }); 
        },  
        init() {  
            window.addEventListener('scroll', () => this.handleScroll());  
            this.handleScroll();  
        }  
    }  
}  

$(document).ready(function(){
    $(".open-menu").click(function(){
        $("body").addClass("overflow-hidden");
        $(".menu").addClass("memushow");
    });

    $(".close-menu").click(function(){
        $("body").removeClass("overflow-hidden");
        $(".menu").removeClass("memushow");
    });
});
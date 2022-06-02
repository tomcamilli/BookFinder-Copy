import { Component, OnInit } from '@angular/core';
import { ActivatedRoute } from '@angular/router';
import { PinchZoomModule } from 'ngx-pinch-zoom';
import { ModalController } from '@ionic/angular';
import { ImageModalPage } from '../image-modal/image-modal.page';




@Component({
  
  selector: 'app-map-display',
  templateUrl: './map-display.page.html',
  styleUrls: ['./map-display.page.scss'],

})
export class MapDisplayPage implements OnInit 
{

  navCtrl: any;
  images = [];
  passImg: any;
  canvasProp: any;
  current_floor = 5;
  plans: Array<string> = [];
  plan_names: Array<string> = ["Lower", "First", "Second", "Third", "Fourth"];
  id: any;
  img_src = "";

  dataRecv = "";
  bookValues: Array<string> = ["", "", "", ""];

  public info: string = ""; //Information variable to display text on the map display page

  constructor(public activeRoute:ActivatedRoute, public modalController: ModalController) {
    
    for(let i = 0; i < 5; i++) {

      let plan_uri = "assets/maps/".concat(i.toString(), ".png");
      this.plans.push(plan_uri);
      this.plan_names[i] = this.plan_names[i] + " Level";
      this.images.push(this.createImage(this.plans[i], this.plan_names[i]));

    }

  }
/*
  async openViewer() {
    const modal = await this.modalController.create({
      component: ViewerModalComponent,
      componentProps: {
        src: this.img_src // required
      },
      cssClass: 'ion-img-viewer', // required
      keyboardClose: true,
      showBackdrop: true
    });

    return await modal.present();
  }
*/
  sliderOpts = {

    zoom:false,
    centeredSlides: true
    
  }; 

  openPreview(canvas) {
    let ctx = canvas.getContext('2d');

    this.modalController.create({
      component: ImageModalPage,
      componentProps: {
        canvas:canvas,
        ctx: this.passImg,
        img_src: this.img_src
      }
    }).then(modal => modal.present());
    
  }

  // This creates the image object that will be displayed to the user later
  createImage(src: string, title: string) {

    let img = new Image();
    img.src = src;
    img.alt = title;
    img.title = title;
    return img;

  }

  // This is legacy code, and useful for trying to get the path of the image outside of the .ts file
  getSrc() {

    return this.img_src;

  }

  // This function is used to increment a marker location's range/side to the next location.
  incrementGap(elementsArray: Array<String>) {
    if (elementsArray[3] == "A") {
      // The book's location is side A, so move to side B and return.
      elementsArray[3] = "B";
      //console.log("It's A, so go to B");
      return elementsArray;
    }
    else {
      // The book's location is side B, so move to side A, increment the range by 1, and return.
      elementsArray[3] = "A";
      elementsArray[2] = (+elementsArray[2] + 1).toString();
      //console.log("It's B, so go to A");
      return elementsArray;
    }
  }

  // This displays the map to the user by using a canvas of the corresponding library floor and drawing the location onto
  // that map
  showFloor(floor_number: number, arr: Array<String>) {

    if(floor_number === 5)
      return;

    let canvas = <HTMLCanvasElement>document.getElementById('canvas');
    let img = this.images[floor_number];
    
    // The canvas/image will take up the entire width of the phone screen and half the height of the phone screen
    // IOS and Android handle their dimensions differently, so this if statement fixes this
    if ( navigator.platform != "iPad" && navigator.platform != "iPhone" && navigator.platform != "iPod" ) {

      canvas.height = window.outerHeight/2;
      canvas.width = window.outerWidth;

    } else {

      canvas.height = screen.height/2;
      canvas.width = screen.width;

    }
    console.log("Floor: " + arr[0] + " Aisle # " + arr[1] + " Range: " + arr[2] + " Side: " + arr[3]);
    // Create a context from the canvas, which it moves and rotates before drawing the floor plan onto it

    let ctx = canvas.getContext("2d");

    // The basement has different dimensions compared to all the other maps we have, so we just scale it
    // slightly different if we're in the basement.
    if (Number(arr[0]) != 0) {
      ctx.scale(0.06, 0.075); //0.07, 0.1
    } else {
      ctx.scale(0.069, 0.09); // 0.069, 0.09
    }
        
    ctx.drawImage(img,0,0); // 0, 0

    var wraparound = false;

    // Splits the range into its start and end components if it is in the form "#-#"
    var rangeArray = arr[2].split("-", 2);
    var rangeStart = +rangeArray[0];
    var rangeEnd = 0;
    if (rangeArray.length > 1) {
      // The range has both start and end components, assign them.
      rangeEnd = +rangeArray[1];
      wraparound = true;
    }
    if (rangeEnd == 0) {
      // The ending range wasn't defined, so make it the same as the start.
      rangeEnd = +arr[2].toString();
    }

    // Splits the side into its start and end components if it is the form "A-B", "B-A", "A-A", "B-B"
    var sideArray = arr[3].split("-", 2);
    var sideStart = sideArray[0];
    var sideEnd = "0";
    if (sideArray.length > 1) {
      // The side has both start and end components, assign them.
      sideEnd = sideArray[1];
      wraparound = true;
    }
    if (sideEnd == "0") {
      // The ending side wasn't defined, so make it the same as the start.
      sideEnd = arr[3].toString();
    }

    // Determine when to start adding marker dots and when to stop.
    var startGap = [arr[0], arr[1], rangeStart.toString(), sideStart.toString()];
    var endGap = [arr[0], arr[1], rangeEnd.toString(), sideEnd.toString()];
    endGap = this.incrementGap(endGap);

    // Loop until the starting location is equal to the ending location
    while(startGap[2] != endGap[2] || startGap[3] != endGap[3]) {
        
        // Variables
        var xoffset = 0;
        var yoffset = 0;

        var xStackJump = 30; // How many pixels is a stack jump

        // low x is left, high x is right
        // low y is top, high y is bottom

        var aisleNum = +arr[1]; // Converts string to integer
        var rangeNum = +startGap[2]; // Converts string to integer

        var aisleSize = 0; // How big the previous aisles are (when to start). We need to remove this number from our range in the Range Algorithm
        var aisleInc = 0; // If the stacks are spaced out more/less in an aisle, change this number
        var aisleFlip = 1; // -1 means the aisle is flipped, 1 means the aisle is facing the right way.
        
        // =================
        // Marker Placement pt.1: Switch Statements
        //      These nested switch statement determine the floor and aisle
        //      of the marker's location, which in turn gives the marker's
        //      starting location and determines some other variables (such
        //      as aisleSize, aisleInc, aisleFlip above) used for the other
        //      algorithms.
        // =================
        switch (arr[0]) // Switch statement for floor
        {
          case '0': // Floor 0 (Basement)
            switch (arr[1]) // Switch statement for aisles
            {
              // Basement is not implemented yet (currently being restructured)
            }
            break;
          case '1': // Floor 1
            switch (arr[1])  // Switch statement for aisles
            {
              case '1': // Aisle 1 (Floor 1)
                xoffset = 2092;
                yoffset = 2715;
                aisleSize = 100;
                break;

              case '2': // Aisle 2 (Floor 1)
                xoffset = 717;
                yoffset = 1922;
                aisleSize = 113;
                aisleFlip = -1; // The second aisle faces the other direction
                break; 
            }
            break;
          case '2': // Floor 2
            switch (arr[1]) // Switch statement for aisles
            { 
              case '1': // Aisle 1 (Floor 2)
                xoffset = 4890;
                yoffset = 3285;
                if (rangeNum >= 5) // Stack jump past range 5
                  yoffset -=xStackJump;
                break;

              case '2': // Aisle 2 (Floor 2)
                xoffset = 4380;
                yoffset = 3285;
                aisleSize = 15; // Previous aisle is 15 stacks
                if (rangeNum >= 20) // Stack jump past range 20
                  yoffset -=xStackJump;
                break;

              case '3': // Aisle 3 (Floor 2)
                xoffset = 3775; 
                yoffset = 3285;
                aisleSize = 30; // Previous aisles are 30 stacks
                break;

              case '4': // Aisle 4 (Floor 2)
                xoffset = 2440;
                yoffset = 3285;
                aisleSize = 34; // Previous aisles are 34 stacks
                break;

              case '5': // Aisle 5 (Floor 2)
                xoffset = 2430; 
                yoffset = 2850; 
                aisleSize = 40; // Previous aisles are 40 stacks
                if (rangeNum >= 41) // Past range 41, the aisle narrows
                {
                  yoffset -= 30;
                  xoffset -= 135;
                }
                if (rangeNum >= 52) // Stack jump past range 52
                  yoffset -=xStackJump;
                break;

              case '6': // Aisle 6 (Floor 2)
                xoffset = 1760;
                yoffset = 3180;
                aisleSize = 55; // Previous aisles are 55 stacks
                if (rangeNum >= 59) // Stack jump past range 59
                  yoffset -=xStackJump;
                break;

              case '7': // Aisle 7 (Floor 2)
                xoffset = 1153;
                yoffset = 3282;
                aisleSize = 69; // Previous aisles are 69 stacks
                if (rangeNum >= 74) // Stack jump past range 74
                  yoffset -=xStackJump;
                if (rangeNum >= 85) // Stack jump past range 85
                {
                  yoffset -=xStackJump;
                }
                break;

              default:
                xoffset = 0;
                yoffset = 0;
                break;
            }      
            break;

          case '3': // Floor 3
            switch (arr[1]) // Switch statement for aisles
            {
              
              case '1': // Aisle 1 (Floor 3)
                xoffset = 4347;
                yoffset = 3389;
                aisleInc += 1; // The stacks in this aisle are a bit more spaced out
                break;

              case '2': // Aisle 2 (Floor 3)
                xoffset = 3700; 
                yoffset = 3279;
                aisleSize = 6; // Previous aisle is 6 stacks
                aisleInc += 1; // The stacks in this aisle are a bit more spaced out
                break;
              
              case '3': // Aisle 3 (Floor 3)
                xoffset = 2490;
                yoffset = 3070;
                aisleSize = 11; // Previous aisles are 11 stacks
                aisleInc += 1; // The stacks in this aisle are a bit more spaced out
                if (rangeNum >= 23) // Stack jump past range 23
                {
                  xoffset -= 125;
                  yoffset -= 15;
                }
                if (rangeNum > 12) // Stack jump and move left past range 12
                {
                  yoffset -= xStackJump;
                  xoffset -= 14;
                }
                break;
              
              case '4': // Aisle 4 (Floor 3)
                xoffset = 1790;
                yoffset = 3080;
                aisleSize = 31; // Previous aisles are 31 stacks
                if (rangeNum >= 43) // Stack jump past range 43
                {
                  xoffset += 25;
                  yoffset -= 55;
                }
                if (rangeNum > 12) // Stack jump past range 12
                {
                  yoffset -= xStackJump;
                }
                break;
              
              case '5': // Aisle 5 (Floor 3)
                xoffset = 1145; 
                yoffset = 3080;
                aisleSize = 45; // Previous aisles are 45 stacks
                if (rangeNum > 12) // Stack jump past range 12
                {
                  yoffset -= xStackJump;
                }
                break;

              default:
                xoffset = 0;
                yoffset = 0;
                break;
            }
            break;

          case '4': // Floor 4
            switch (arr[1]) // Switch statement for aisles
            {
              case '1': // Aisle 1 (Floor 4)
                xoffset = 4640;
                yoffset = 3180;
                break;
            
              case '2': // Aisle 2
                xoffset = 2350;
                yoffset = 1330;
                aisleSize = 13; // Previous aisle is 13 stacks
                aisleFlip = -1; // This aisle is facing the opposite direction
                aisleInc -= 10; // Stacks in this aisle are closer together
                if (rangeNum >= 16) // Aisle jumps to new location after range 16
                {
                  xoffset += 82;
                  yoffset += 540;
                  aisleInc += 15;
                }
                break;
              
              default:
                xoffset = 0;
                yoffset = 0;
                break;
            }
            break;
        }
        
        // =================
        // Marker Placement pt.2: Range Algorithm
        //      These statements move the marker's position a certain distance
        //      depending on the range. It uses a number of variables to narrow
        //      the book's location to a single stack (defaults on side A):
        //          startGap[2] is the range value
        //          aisleSize is how big the previous stacks were that we need to subtract from the range
        //          aisleInc is how spaced out the stacks are
        //          aisleFlip is the direction the aisle is facing.
        // =================
        if (arr[0] == '1')
        {
          // Floor 1 uses horizontal-facing aisles, so we change the xoffset instead of the yoffset
          xoffset = xoffset - (Number(+startGap[2]-aisleSize) * (109+aisleInc) * aisleFlip) + 108; // Number is the distance between the shelves.
        }
        else
        {
          // The rest of the floors use vertical-facing aisles, so we change the yoffset
          yoffset = yoffset - (Number(+startGap[2]-aisleSize) * (109+aisleInc) * aisleFlip) + 108; // Number is the distance between the shelves.
        }

        // If there's a wraparound, we have to move the yoffset up to account for this.
        if (wraparound)
        {
          yoffset -= 25;
        }
        
        // =================
        // Marker Placement pt.3: Side Algorithm
        //      This simple nested if-statement determines if a book is located
        //      on the B (far) side of a stack, then we need to move the marker
        //      accordingly so it is on that side.
        //
        //      We must also make sure that we account for direction (aisleFlip)
        //      in case the aisle is facing the opposite direction like on the
        //      fourth floor
        // =================
        if (arr[3] == 'B') 
        {
          if (arr[0] == '1')
          {
            // Floor 1 uses horziontal-facing aisles, so we change the xoffset instead of the yoffset
            xoffset = xoffset - (50*aisleFlip);
          }
          else
          {
            // The rest of the floors use vertical-facing aisles, so we change the yoffset
            yoffset = yoffset - (35*aisleFlip); 
          }
        }

        // On the first floor, it is possible the range is "15.5" which is a one-sided stack at the end of the first aisle (labelled "15 C" in the database)
        if (arr[2] == '15.5')
        {
          // We need to change the marker's position in this case
          yoffset = yoffset - 60;
          xoffset = xoffset + 165;
        }
    
        console.log("xOffSet: " + xoffset + " yOffset: " + yoffset);
        console.log("imgh" + img.height + " imgw" + img.width);
        ctx.beginPath(); //Canvas/Image dimensions: 375(width) by 406(height) 
        ctx.arc(xoffset, yoffset, 40, 0, 2 * Math.PI);// 325, 275, 5, 0, 2 * Math.Pi
        
        ctx.fillStyle = "red";
        ctx.fill();

        var myImage = canvas.toDataURL("image/png");
        //var imageElement = document.getElementById('canvas');
        //imageElement.src = myImage;
        this.passImg = this.createImage(myImage, "new image")
        this.img_src = myImage;
        this.canvasProp = canvas;

        // Increment the marker's location in the case of wraparound.
        startGap = this.incrementGap(startGap);
      }
        
    } 

  // This controls the text that appears below the map
  decode(arr: Array<String>) {

      var formattedSides = "";
      var firstVal = 0;
      var lastVal = 0;
      var ranges;
      var sides;
      var isWrapAround = false;

     // This will need to be reworked but this is what prints the expanded version of Sides
     if (arr[3].includes("-")) { // Checks to see if there is a dash i.e. a wraparound
       
        ranges = arr[2].split("-", 2);
        sides = arr[3].split("-", 2);
        firstVal = ranges[0];
        lastVal = ranges[1];
        var currVal = firstVal;
        var currSide = sides[0];
        while (true) {

          if (currVal == lastVal && currSide === sides[1]) {
            formattedSides += (currVal + currSide);
            break;

          }

          formattedSides += (currVal + currSide + ", ");

          if (currSide === "A") {
            currSide = "B";
          } else {
            currSide = "A";
            currVal++;

          }


        }

        isWrapAround = true;
      }

      var floorNumber;

      if (Number(arr[0]) == 0) {

        floorNumber = "Basement";

      } else if (Number(arr[0]) == 1) {

        floorNumber = "1st";

      } else if (Number(arr[0]) == 2) {

        floorNumber = "2nd";

      } else if (Number(arr[0]) == 3) {

        floorNumber = "3rd";

      } else if (Number(arr[0]) == 4) {

        floorNumber = "4th";

      }

      // If the side is "C", which is the case of Floor 2, Aisle 1, Range 15,
      // we need to change it to Range 15.5 and Side A, which is the proper label for this stack's location
      if (arr[3] == "C")
      {
        arr[2] = "15.5";
        arr[3] = "A";
      }
      
      this.info = this.info + "Call Number:" + '\n';
      this.info.fontcolor("white");
      this.info = this.info + '\t' + "Floor: " + floorNumber + '\n';
      this.info = this.info + '\t' + "Aisle #: " + arr[1] + '\n';
      this.info = this.info + '\t' + "Range: " + arr[2] + '\n';
      this.info = this.info + '\t' + "Side: " + arr[3] + '\n';

      if (isWrapAround) { // This expanded version will only appear if we experience a wraparound
        this.info = this.info + '\t' + '\t' + "(" + formattedSides + ")" + '\n';
      }
      this.info = this.info + '\t' + "Collection: " + arr[4] + '\n';
      
  }
  
  // This is what runs when the new page loads. It receives data from the script and displays the map after half a second
  ngOnInit() {

    this.dataRecv = this.activeRoute.snapshot.paramMap.get('data');
    this.dataRecv = this.dataRecv.substr(1);
    this.bookValues = this.dataRecv.split(",", 12);
    this.decode(this.bookValues);

    console.log("Book Values" + this.bookValues);

    /* Testing **********/
    //this.bookValues[0] = '2'; // Floor
    //this.bookValues[1] = '2'; // Aisle
    //this.bookValues[2] = '1'; // Range
    //this.bookValues[3] = 'A'; // Side
    /********************/

    
    // This delays the loading of the image by half a second so that the phone has the chance to fully load this page before 
    // trying to load the image
    setTimeout(() => this.showFloor(Number(this.bookValues[0]), this.bookValues), 500);
  }

}

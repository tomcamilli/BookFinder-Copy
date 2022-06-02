import { Component, OnInit } from '@angular/core';
import { NavParams, ModalController } from '@ionic/angular';

@Component({
  selector: 'app-image-modal',
  templateUrl: './image-modal.page.html',
  styleUrls: ['./image-modal.page.scss'],
})
export class ImageModalPage implements OnInit {
  canvas: any;
  img: any;
  img_src: any;

  /* We come here when the user taps on any image in the app.
  * These are the arguments you can use with thise sliderOpts
  * feature.
  */
  sliderOpts = {
    zoom: {
      maxRatio: 3
    },
    centeredSlides: true
    
  };

  constructor(private navParams: NavParams, private modalController: ModalController) { }

  ngOnInit() {
    /* When this page loads, it will grab the values that were passed to this page
    * You might wonder why we use a canvas on this page but this is because this file
    * is more or less a template. We use this for the help page but we also use this
    * for the image we create and draw on map-display. 
    */
    this.canvas = this.navParams.get('canvas');
    this.img = this.navParams.get('ctx');
    this.img_src = this.navParams.get('img_src');
  }
/*
  zoom(zoomIn: boolean) {
  


  }
*/
  getSrc() {

    return this.img_src;

  }
  close() {

    this.modalController.dismiss();

  }

}

import { Component, Input, OnInit } from '@angular/core';
import { NavController,ModalController } from '@ionic/angular';
import { HelpImageModalPage } from '../help-image-modal/help-image-modal.page';

@Component({
  selector: 'app-modal',
  templateUrl: './modal.page.html',
  styleUrls: ['./modal.page.scss'],
})
export class ModalPage implements OnInit {

  img: any;

  sliderOpts = {

    zoom:false,
    centeredSlides: true
    
  };

  constructor(private nav:NavController,private modalController:ModalController) { }

  ngOnInit() {

  }

  /* This takes in an image and what this does is load a page in which the image is the only element
  * This is under image-modal folder, but we're passing the image the user pressed as a parameter
  * for this new page
  */
  openPreview(img) {
    this.img = "assets/help/helpPage".concat(img.toString(), ".PNG");
    this.modalController.create({

      component: HelpImageModalPage,
      componentProps: {
        img: this.img
      }
    }).then(modal => modal.present());
  }

  closeModal()
  {
    this.modalController.dismiss();
  }

}


import { Component, OnInit } from '@angular/core';
import { NavParams, ModalController } from '@ionic/angular';

@Component({
  selector: 'app-help-image-modal',
  templateUrl: './help-image-modal.page.html',
  styleUrls: ['./help-image-modal.page.scss'],
})
export class HelpImageModalPage implements OnInit {
  
  img: any;

  constructor(private navParams: NavParams, private modalController: ModalController) { }

  ngOnInit() {

    this.img = this.navParams.get('img');

  }

  getSrc() {

    return this.img;

  }
  

  close() {

    this.modalController.dismiss();

  }

}

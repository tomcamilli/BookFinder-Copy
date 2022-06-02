import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';
import { Routes, RouterModule } from '@angular/router';

import { IonicModule } from '@ionic/angular';

import { HelpImageModalPage } from './help-image-modal.page';
import { PinchZoomModule } from 'ngx-pinch-zoom';

const routes: Routes = [
  {
    path: '',
    component: HelpImageModalPage
  }
];

@NgModule({
  imports: [
    CommonModule,
    FormsModule,
    PinchZoomModule,
    IonicModule,
    RouterModule.forChild(routes)
  ],
  declarations: [HelpImageModalPage]
})
export class HelpImageModalPageModule {}

import { NgModule, NO_ERRORS_SCHEMA } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';
import { Routes, RouterModule } from '@angular/router';

import { IonicModule } from '@ionic/angular';

import { ImageModalPage } from './image-modal.page';

import { PinchZoomModule, PinchZoomComponent } from 'ngx-pinch-zoom';

const routes: Routes = [
  {
    path: '',
    component: ImageModalPage
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
  declarations: [ImageModalPage],
  schemas: [NO_ERRORS_SCHEMA]
})
export class ImageModalPageModule {}

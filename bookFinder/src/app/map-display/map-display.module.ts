import { NgModule, NO_ERRORS_SCHEMA } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';
import { Routes, RouterModule } from '@angular/router';

import { IonicModule } from '@ionic/angular';

import { MapDisplayPage } from './map-display.page';
import { PinchZoomModule, PinchZoomComponent } from 'ngx-pinch-zoom';

const routes: Routes = [
  {
    path: '',
    component: MapDisplayPage
  }
];

@NgModule({
  imports: [
    CommonModule,
    FormsModule,
    PinchZoomModule,
    IonicModule,
    RouterModule.forChild(routes),
  ],
  declarations: [MapDisplayPage],
  schemas: [NO_ERRORS_SCHEMA]
})
export class MapDisplayPageModule {}

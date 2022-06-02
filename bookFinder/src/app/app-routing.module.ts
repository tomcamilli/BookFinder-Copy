import { NgModule } from '@angular/core';
import { Routes, RouterModule } from '@angular/router';
import { ModalPageModule } from './modal/modal.module';
import { HomePageModule } from './home/home.module';
import { MapDisplayPageModule } from './map-display/map-display.module';

const routes: Routes = [
  { path: '', redirectTo: 'home', pathMatch: 'full' },
  { path: 'home', loadChildren: './home/home.module#HomePageModule' },
  { path: 'map-display/:data', loadChildren: './map-display/map-display.module#MapDisplayPageModule' },
  { path: 'modal-page', loadChildren: './modal/modal.module' },
  { path: 'modal', loadChildren: './modal/modal.module' },  { path: 'image-modal', loadChildren: './image-modal/image-modal.module#ImageModalPageModule' },
  { path: 'help-image-modal', loadChildren: './help-image-modal/help-image-modal.module#HelpImageModalPageModule' },


];

@NgModule({
  imports: [RouterModule.forRoot(routes), ModalPageModule],
  exports: [RouterModule]
})
export class AppRoutingModule { }

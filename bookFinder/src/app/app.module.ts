import { NgModule } from '@angular/core';
import { BrowserModule } from '@angular/platform-browser';
import { RouteReuseStrategy } from '@angular/router';

import { IonicModule, IonicRouteStrategy } from '@ionic/angular';
import { SplashScreen } from '@ionic-native/splash-screen/ngx';
import { StatusBar } from '@ionic-native/status-bar/ngx';

import { AppComponent } from './app.component';
import { AppRoutingModule } from './app-routing.module';
import { HttpClientModule } from '@angular/common/http';

import { HttpModule } from '@angular/http';
import { ScModalModule } from 'angular-5-popup';
import { ModalPageModule } from './modal/modal.module'
import { ImageModalPageModule } from './image-modal/image-modal.module';
import { HelpImageModalPageModule } from './help-image-modal/help-image-modal.module';




@NgModule({
  declarations: [AppComponent],
  entryComponents: [],
  imports: [
    BrowserModule, 
    HttpClientModule, 
    IonicModule.forRoot(),  
    AppRoutingModule, 
    HttpModule, 
    ScModalModule, 
    HelpImageModalPageModule,
    ModalPageModule,
    ImageModalPageModule
  ],
  providers: [
    StatusBar,
    SplashScreen,
    { provide: RouteReuseStrategy, useClass: IonicRouteStrategy },
    
  ],
  bootstrap: [AppComponent]
})
export class AppModule {}

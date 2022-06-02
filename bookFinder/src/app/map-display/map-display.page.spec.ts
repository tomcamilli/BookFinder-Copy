import { CUSTOM_ELEMENTS_SCHEMA } from '@angular/core';
import { async, ComponentFixture, TestBed } from '@angular/core/testing';

import { MapDisplayPage } from './map-display.page';

describe('MapDisplayPage', () => {
  let component: MapDisplayPage;
  let fixture: ComponentFixture<MapDisplayPage>;

  beforeEach(async(() => {
    TestBed.configureTestingModule({
      declarations: [ MapDisplayPage ],
      schemas: [CUSTOM_ELEMENTS_SCHEMA],
    })
    .compileComponents();
  }));

  beforeEach(() => {
    fixture = TestBed.createComponent(MapDisplayPage);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});

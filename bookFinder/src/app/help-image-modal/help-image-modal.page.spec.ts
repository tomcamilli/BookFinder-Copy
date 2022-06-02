import { CUSTOM_ELEMENTS_SCHEMA } from '@angular/core';
import { async, ComponentFixture, TestBed } from '@angular/core/testing';

import { HelpImageModalPage } from './help-image-modal.page';

describe('HelpImageModalPage', () => {
  let component: HelpImageModalPage;
  let fixture: ComponentFixture<HelpImageModalPage>;

  beforeEach(async(() => {
    TestBed.configureTestingModule({
      declarations: [ HelpImageModalPage ],
      schemas: [CUSTOM_ELEMENTS_SCHEMA],
    })
    .compileComponents();
  }));

  beforeEach(() => {
    fixture = TestBed.createComponent(HelpImageModalPage);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});

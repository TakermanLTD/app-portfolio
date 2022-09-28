import React from "react";

export default function Services() {
  return (
    <section id="services" className="services section-bg">
      <div className="container" data-aos="fade-up">

        <div className="section-title">
          <h2>Services</h2>
          <p>
            Nowadays the automation become an important part of our lives as the world started to move faster
            and people want to spend less time in their days with basic tasks. We are proud to say that our people
            can develop and automate your needs with software solutions.
          </p>
        </div>

        <div className="row">
          {/* <!-- <div className="col-xl-3 col-md-6 d-flex align-items-stretch" data-aos="zoom-in" data-aos-delay="100">
            <div className="icon-box">
              <div className="icon"><i className="bx bxl-dribbble"></i></div>
              <h4><a href="">Lorem Ipsum</a></h4>
              <p>Voluptatum deleniti atque corrupti quos dolores et quas molestias excepturi</p>
            </div>
          </div> --> */}

          <div id="development" className="col-xl-3 col-md-6 d-flex align-items-stretch mt-4 mt-md-0" data-aos="zoom-in"
            data-aos-delay="200">
            <div className="icon-box">
              <div className="icon"><i className="bx bx-file"></i></div>
              <h4><a href="#development">Software development</a></h4>
              <p>Professional architecting of software, development of components and websites. Usage of different types
                of architectures, patterns, technologies and tools.</p>
            </div>
          </div>

          <div id="testing" className="col-xl-3 col-md-6 d-flex align-items-stretch mt-4 mt-xl-0" data-aos="zoom-in"
            data-aos-delay="300">
            <div className="icon-box">
              <div className="icon"><i className="bx bx-tachometer"></i></div>
              <h4><a href="#testing">Testing and Optimisation</a></h4>
              <p>Automated and manual testing before and after delivery. Performance testing and optimisation of load
                time.</p>
            </div>
          </div>

          <div id="delivery" className="col-xl-3 col-md-6 d-flex align-items-stretch mt-4 mt-xl-0" data-aos="zoom-in"
            data-aos-delay="400">
            <div className="icon-box">
              <div className="icon"><i className="bx bx-layer"></i></div>
              <h4><a href="#delivery">Delivery</a></h4>
              <p>
                Planing of the deployment infrastructure depend of the needs of the project and client. Usage of Cloud
                providers or usage of own Infrastructure. Creation of Services map.
              </p>
            </div>
          </div>

        </div>

      </div>
    </section>
  );
}
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="FlowSync - Streamline your team's workflow with our all-in-one project management solution. Boost productivity and collaboration.">
    <meta name="keywords" content="project management, team collaboration, workflow automation, SaaS">
    <title>FlowSync - Modern Project Management for Teams</title>

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">

    <style>
        /* Custom CSS - Minimal and focused */
        :root {
            --primary-color: #052212;
            --secondary-color: #13a161;
            --accent-color: #c5236d;
            --light-bg: #f8f9fa;
            --dark-text: #212529;
            --light-text: #6c757d;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
            color: var(--dark-text);
        }

        /* Sticky navbar styling */
        .navbar {
            transition: all 0.3s ease;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        .navbar-brand {
            font-weight: 700;
            font-size: 1.5rem;
            color: var(--primary-color) !important;
        }

        .nav-link {
            font-weight: 500;
            margin: 0 0.5rem;
            transition: color 0.3s ease;
        }

        .nav-link:hover {
            color: var(--primary-color) !important;
        }

        .btn-primary {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
            font-weight: 600;
            padding: 0.5rem 1.5rem;
            border-radius: 50px;
            transition: all 0.3s ease;
        }

        .btn-primary:hover {
            background-color: var(--secondary-color);
            border-color: var(--secondary-color);
            transform: translateY(-2px);
        }

        .btn-outline-primary {
            border-color: var(--primary-color);
            color: var(--primary-color);
            font-weight: 600;
            padding: 0.5rem 1.5rem;
            border-radius: 50px;
            transition: all 0.3s ease;
        }

        .btn-outline-primary:hover {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
            transform: translateY(-2px);
        }

        /* Hero section styling */
        .hero {
            padding: 5rem 0;
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
        }

        .hero h1 {
            font-size: 3rem;
            font-weight: 700;
            margin-bottom: 1.5rem;
            color: var(--dark-text);
        }

        .hero p {
            font-size: 1.25rem;
            margin-bottom: 2rem;
            color: var(--light-text);
        }

        .hero-image {
            max-width: 100%;
            height: auto;
            border-radius: 10px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        }

        /* Section styling */
        section {
            padding: 4rem 0;
        }

        .section-title {
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 1rem;
            text-align: center;
        }

        .section-subtitle {
            font-size: 1.1rem;
            color: var(--light-text);
            text-align: center;
            margin-bottom: 3rem;
        }

        /* Feature cards */
        .feature-card {
            padding: 2rem;
            border-radius: 10px;
            height: 100%;
            transition: all 0.3s ease;
            border: 1px solid #e9ecef;
        }

        .feature-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.1);
        }

        .feature-icon {
            font-size: 3rem;
            color: var(--primary-color);
            margin-bottom: 1rem;
        }

        /* How it works */
        .step {
            text-align: center;
            position: relative;
            padding: 2rem;
        }

        .step-number {
            width: 60px;
            height: 60px;
            background-color: var(--primary-color);
            color: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            font-weight: 700;
            margin: 0 auto 1rem;
        }

        /* Pricing cards */
        .pricing-card {
            border-radius: 10px;
            padding: 2rem;
            height: 100%;
            transition: all 0.3s ease;
            border: 1px solid #e9ecef;
        }

        .pricing-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.1);
        }

        .pricing-card.featured {
            border: 2px solid var(--primary-color);
            transform: scale(1.05);
        }

        .pricing-card.featured .badge {
            position: absolute;
            top: -15px;
            right: 20px;
            padding: 0.5rem 1rem;
            background-color: var(--accent-color);
            color: white;
            border-radius: 50px;
            font-weight: 600;
        }

        .price {
            font-size: 3rem;
            font-weight: 700;
            color: var(--primary-color);
        }

        .price-period {
            font-size: 1rem;
            color: var(--light-text);
        }

        /* Testimonials */
        .testimonial-card {
            background-color: white;
            border-radius: 10px;
            padding: 2rem;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
            height: 100%;
        }

        .testimonial-text {
            font-style: italic;
            margin-bottom: 1.5rem;
            color: var(--light-text);
        }

        .testimonial-author {
            display: flex;
            align-items: center;
        }

        .testimonial-avatar {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            margin-right: 1rem;
            object-fit: cover;
        }

        /* FAQ */
        .accordion-button:not(.collapsed) {
            background-color: rgba(67, 97, 238, 0.1);
            color: var(--primary-color);
        }

        .accordion-button:focus {
            box-shadow: none;
            border-color: rgba(67, 97, 238, 0.25);
        }

        /* CTA section */
        .cta {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
            color: white;
            padding: 4rem 0;
            border-radius: 10px;
            margin: 4rem 0;
        }

        .cta h2 {
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 1rem;
        }

        .cta p {
            font-size: 1.2rem;
            margin-bottom: 2rem;
            opacity: 0.9;
        }

        .btn-light {
            background-color: white;
            color: var(--primary-color);
            font-weight: 600;
            padding: 0.75rem 2rem;
            border-radius: 50px;
            border: none;
            transition: all 0.3s ease;
        }

        .btn-light:hover {
            background-color: #f8f9fa;
            transform: translateY(-2px);
        }

        /* Footer */
        footer {
            background-color: var(--light-bg);
            padding: 3rem 0 1.5rem;
        }

        .footer-brand {
            font-weight: 700;
            font-size: 1.5rem;
            color: var(--primary-color);
            margin-bottom: 1rem;
        }

        .footer-links a {
            color: var(--light-text);
            text-decoration: none;
            margin-bottom: 0.5rem;
            display: block;
            transition: color 0.3s ease;
        }

        .footer-links a:hover {
            color: var(--primary-color);
        }

        .copyright {
            color: var(--light-text);
            font-size: 0.9rem;
            margin-top: 2rem;
            padding-top: 2rem;
            border-top: 1px solid #dee2e6;
        }

        /* Social proof logos */
        .trusted-by {
            background-color: white;
            padding: 3rem 0;
        }

        .logo-container {
            display: flex;
            justify-content: space-around;
            align-items: center;
            flex-wrap: wrap;
            opacity: 0.7;
        }

        .logo-placeholder {
            width: 120px;
            height: 40px;
            background-color: #e9ecef;
            border-radius: 5px;
            margin: 0.5rem;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--light-text);
            font-weight: 600;
        }

        /* Responsive adjustments */
        @media (max-width: 768px) {
            .hero h1 {
                font-size: 2rem;
            }

            .hero p {
                font-size: 1rem;
            }

            .section-title {
                font-size: 2rem;
            }

            .price {
                font-size: 2rem;
            }

            .cta h2 {
                font-size: 1.8rem;
            }
        }
    </style>
</head>
<body>
<!-- Sticky Navigation Bar -->
<nav class="navbar navbar-expand-lg navbar-light bg-white sticky-top">
    <div class="container">
        <a class="navbar-brand" href="#">FlowSync</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item">
                    <a class="nav-link" href="#features">Features</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#pricing">Pricing</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#testimonials">Testimonials</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#faq">FAQ</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#contact">Contact</a>
                </li>
            </ul>
{{--            <a href="#cta" class="btn btn-primary ms-lg-3">Get Started</a>--}}
            @if(Auth::check())
                <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-primary ms-2">Dashboard</a>
            @else
                <a href="{{ route('login') }}" class="btn btn-outline-primary ms-2">Login</a>
            @endif
        </div>
    </div>
</nav>

<!-- Hero Section -->
<section class="hero">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6">
                <h1>Streamline Your Team's Workflow with FlowSync</h1>
                <p>The all-in-one project management solution that helps teams collaborate better, deliver faster, and achieve more together.</p>
                <div class="d-flex flex-column flex-sm-row gap-3">
                    <a href="#cta" class="btn btn-primary btn-lg">Start Free Trial</a>
                    <a href="#demo" class="btn btn-outline-primary btn-lg">View Demo</a>
                </div>
            </div>
            <div class="col-lg-6 mt-5 mt-lg-0">
{{--                <img src="" alt="FlowSync Dashboard" class="hero-image">--}}
            </div>
        </div>
    </div>
</section>

<!-- Trusted By / Social Proof -->
<section class="trusted-by">
    <div class="container">
        <p class="text-center mb-4 text-muted">Trusted by leading companies worldwide</p>
        <div class="logo-container">
            <div class="logo-placeholder">TechCorp</div>
            <div class="logo-placeholder">InnovateLab</div>
            <div class="logo-placeholder">DataFlow</div>
            <div class="logo-placeholder">CloudBase</div>
            <div class="logo-placeholder">NextGen</div>
        </div>
    </div>
</section>

<!-- Features Section -->
<section id="features" class="bg-light">
    <div class="container">
        <h2 class="section-title">Powerful Features for Modern Teams</h2>
        <p class="section-subtitle">Everything you need to manage projects efficiently and effectively</p>

        <div class="row g-4">
            <div class="col-md-6 col-lg-4">
                <div class="feature-card bg-white">
                    <div class="feature-icon">
                        <i class="bi bi-kanban"></i>
                    </div>
                    <h3>Visual Project Boards</h3>
                    <p>Organize tasks with customizable Kanban boards that adapt to your workflow. Drag and drop cards to update status instantly.</p>
                </div>
            </div>
            <div class="col-md-6 col-lg-4">
                <div class="feature-card bg-white">
                    <div class="feature-icon">
                        <i class="bi bi-people"></i>
                    </div>
                    <h3>Team Collaboration</h3>
                    <p>Real-time updates, comments, and notifications keep everyone in sync. Share files, assign tasks, and track progress together.</p>
                </div>
            </div>
            <div class="col-md-6 col-lg-4">
                <div class="feature-card bg-white">
                    <div class="feature-icon">
                        <i class="bi bi-graph-up"></i>
                    </div>
                    <h3>Advanced Analytics</h3>
                    <p>Gain insights with detailed reports on project progress, team performance, and resource utilization to make data-driven decisions.</p>
                </div>
            </div>
            <div class="col-md-6 col-lg-4">
                <div class="feature-card bg-white">
                    <div class="feature-icon">
                        <i class="bi bi-clock-history"></i>
                    </div>
                    <h3>Time Tracking</h3>
                    <p>Monitor time spent on tasks and projects with built-in time tracking. Generate accurate reports for billing and productivity analysis.</p>
                </div>
            </div>
            <div class="col-md-6 col-lg-4">
                <div class="feature-card bg-white">
                    <div class="feature-icon">
                        <i class="bi bi-plugin"></i>
                    </div>
                    <h3>Integrations</h3>
                    <p>Connect with your favorite tools like Slack, GitHub, Google Drive, and more. Automate workflows and eliminate context switching.</p>
                </div>
            </div>
            <div class="col-md-6 col-lg-4">
                <div class="feature-card bg-white">
                    <div class="feature-icon">
                        <i class="bi bi-shield-check"></i>
                    </div>
                    <h3>Enterprise Security</h3>
                    <p>Keep your data safe with enterprise-grade security, including SSO, role-based permissions, and regular security audits.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- How It Works -->
<section id="how-it-works">
    <div class="container">
        <h2 class="section-title">How FlowSync Works</h2>
        <p class="section-subtitle">Get started in minutes, not weeks</p>

        <div class="row g-4">
            <div class="col-md-4">
                <div class="step">
                    <div class="step-number">1</div>
                    <h3>Sign Up & Set Up</h3>
                    <p>Create your account in seconds and invite your team members. Customize your workspace to match your workflow.</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="step">
                    <div class="step-number">2</div>
                    <h3>Create Projects</h3>
                    <p>Set up projects, create tasks, and assign team members. Use templates to get started quickly or build from scratch.</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="step">
                    <div class="step-number">3</div>
                    <h3>Track & Collaborate</h3>
                    <p>Monitor progress, communicate with your team, and deliver projects on time. Access your work from anywhere, on any device.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Pricing Section -->
<section id="pricing" class="bg-light">
    <div class="container">
        <h2 class="section-title">Simple, Transparent Pricing</h2>
        <p class="section-subtitle">Choose the plan that fits your team's needs</p>

        <div class="row g-4 align-items-center">
            <div class="col-lg-4">
                <div class="pricing-card bg-white">
                    <h3 class="text-center">Starter</h3>
                    <div class="text-center my-4">
                        <span class="price">$0</span>
                        <span class="price-period">/month</span>
                    </div>
                    <ul class="list-unstyled">
                        <li class="mb-2"><i class="bi bi-check-circle-fill text-success me-2"></i>Up to 5 users</li>
                        <li class="mb-2"><i class="bi bi-check-circle-fill text-success me-2"></i>10 projects</li>
                        <li class="mb-2"><i class="bi bi-check-circle-fill text-success me-2"></i>Basic task management</li>
                        <li class="mb-2"><i class="bi bi-check-circle-fill text-success me-2"></i>2GB storage</li>
                        <li class="mb-2"><i class="bi bi-check-circle-fill text-success me-2"></i>Email support</li>
                    </ul>
                    <div class="d-grid mt-4">
                        <a href="#cta" class="btn btn-outline-primary">Get Started</a>
                    </div>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="pricing-card bg-white featured position-relative">
                    <span class="badge">Most Popular</span>
                    <h3 class="text-center">Pro</h3>
                    <div class="text-center my-4">
                        <span class="price">$12</span>
                        <span class="price-period">/user/month</span>
                    </div>
                    <ul class="list-unstyled">
                        <li class="mb-2"><i class="bi bi-check-circle-fill text-success me-2"></i>Unlimited users</li>
                        <li class="mb-2"><i class="bi bi-check-circle-fill text-success me-2"></i>Unlimited projects</li>
                        <li class="mb-2"><i class="bi bi-check-circle-fill text-success me-2"></i>Advanced features</li>
                        <li class="mb-2"><i class="bi bi-check-circle-fill text-success me-2"></i>100GB storage</li>
                        <li class="mb-2"><i class="bi bi-check-circle-fill text-success me-2"></i>Priority support</li>
                        <li class="mb-2"><i class="bi bi-check-circle-fill text-success me-2"></i>Integrations</li>
                    </ul>
                    <div class="d-grid mt-4">
                        <a href="#cta" class="btn btn-primary">Start Free Trial</a>
                    </div>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="pricing-card bg-white">
                    <h3 class="text-center">Enterprise</h3>
                    <div class="text-center my-4">
                        <span class="price">Custom</span>
                    </div>
                    <ul class="list-unstyled">
                        <li class="mb-2"><i class="bi bi-check-circle-fill text-success me-2"></i>Everything in Pro</li>
                        <li class="mb-2"><i class="bi bi-check-circle-fill text-success me-2"></i>Unlimited storage</li>
                        <li class="mb-2"><i class="bi bi-check-circle-fill text-success me-2"></i>Advanced security</li>
                        <li class="mb-2"><i class="bi bi-check-circle-fill text-success me-2"></i>SSO & SAML</li>
                        <li class="mb-2"><i class="bi bi-check-circle-fill text-success me-2"></i>Dedicated account manager</li>
                        <li class="mb-2"><i class="bi bi-check-circle-fill text-success me-2"></i>Custom training</li>
                    </ul>
                    <div class="d-grid mt-4">
                        <a href="#contact" class="btn btn-outline-primary">Contact Sales</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Testimonials -->
<section id="testimonials">
    <div class="container">
        <h2 class="section-title">What Our Customers Say</h2>
        <p class="section-subtitle">Join thousands of teams already using FlowSync</p>

        <div class="row g-4">
            <div class="col-lg-4">
                <div class="testimonial-card">
                    <p class="testimonial-text">"FlowSync has transformed how our team collaborates. We've cut project delivery time by 30% and improved communication across departments."</p>
                    <div class="testimonial-author">
                        <img src="https://picsum.photos/seed/user1/50/50.jpg" alt="Sarah Johnson" class="testimonial-avatar">
                        <div>
                            <h6 class="mb-0">Sarah Johnson</h6>
                            <small class="text-muted">Project Manager, TechCorp</small>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="testimonial-card">
                    <p class="testimonial-text">"The analytics features in FlowSync give us insights we never had before. We can now make data-driven decisions that improve our processes."</p>
                    <div class="testimonial-author">
                        <img src="https://picsum.photos/seed/user2/50/50.jpg" alt="Michael Chen" class="testimonial-avatar">
                        <div>
                            <h6 class="mb-0">Michael Chen</h6>
                            <small class="text-muted">CTO, InnovateLab</small>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="testimonial-card">
                    <p class="testimonial-text">"We tried several project management tools, but FlowSync is the only one that our entire team actually enjoys using. It's intuitive and powerful."</p>
                    <div class="testimonial-author">
                        <img src="https://picsum.photos/seed/user3/50/50.jpg" alt="Emily Rodriguez" class="testimonial-avatar">
                        <div>
                            <h6 class="mb-0">Emily Rodriguez</h6>
                            <small class="text-muted">Design Director, Creative Studio</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- FAQ Section -->
<section id="faq" class="bg-light">
    <div class="container">
        <h2 class="section-title">Frequently Asked Questions</h2>
        <p class="section-subtitle">Got questions? We've got answers</p>

        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="accordion" id="faqAccordion">
                    <div class="accordion-item mb-3">
                        <h2 class="accordion-header" id="headingOne">
                            <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                                Is there a free trial available?
                            </button>
                        </h2>
                        <div id="collapseOne" class="accordion-collapse collapse show" aria-labelledby="headingOne" data-bs-parent="#faqAccordion">
                            <div class="accordion-body">
                                Yes! We offer a 14-day free trial of our Pro plan with no credit card required. After the trial, you can choose to upgrade or continue with our free Starter plan.
                            </div>
                        </div>
                    </div>
                    <div class="accordion-item mb-3">
                        <h2 class="accordion-header" id="headingTwo">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
                                Can I change my plan later?
                            </button>
                        </h2>
                        <div id="collapseTwo" class="accordion-collapse collapse" aria-labelledby="headingTwo" data-bs-parent="#faqAccordion">
                            <div class="accordion-body">
                                Absolutely! You can upgrade or downgrade your plan at any time from your account settings. Changes will be reflected in your next billing cycle.
                            </div>
                        </div>
                    </div>
                    <div class="accordion-item mb-3">
                        <h2 class="accordion-header" id="headingThree">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseThree" aria-expanded="false" aria-controls="collapseThree">
                                Does FlowSync integrate with other tools?
                            </button>
                        </h2>
                        <div id="collapseThree" class="accordion-collapse collapse" aria-labelledby="headingThree" data-bs-parent="#faqAccordion">
                            <div class="accordion-body">
                                Yes, FlowSync integrates with over 50 popular tools including Slack, GitHub, Google Drive, Dropbox, and more. We're constantly adding new integrations based on customer feedback.
                            </div>
                        </div>
                    </div>
                    <div class="accordion-item mb-3">
                        <h2 class="accordion-header" id="headingFour">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseFour" aria-expanded="false" aria-controls="collapseFour">
                                Is my data secure?
                            </button>
                        </h2>
                        <div id="collapseFour" class="accordion-collapse collapse" aria-labelledby="headingFour" data-bs-parent="#faqAccordion">
                            <div class="accordion-body">
                                Security is our top priority. We use industry-standard encryption for data in transit and at rest, conduct regular security audits, and comply with GDPR, CCPA, and other privacy regulations.
                            </div>
                        </div>
                    </div>
                    <div class="accordion-item mb-3">
                        <h2 class="accordion-header" id="headingFive">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseFive" aria-expanded="false" aria-controls="collapseFive">
                                Do you offer training for teams?
                            </button>
                        </h2>
                        <div id="collapseFive" class="accordion-collapse collapse" aria-labelledby="headingFive" data-bs-parent="#faqAccordion">
                            <div class="accordion-body">
                                Yes, we offer comprehensive onboarding and training for Enterprise customers. Pro plan users have access to our knowledge base, video tutorials, and weekly webinars.
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Final Call to Action -->
<section id="cta" class="container">
    <div class="cta text-center">
        <h2>Ready to Transform Your Team's Workflow?</h2>
        <p>Join thousands of teams already using FlowSync to deliver projects faster and more efficiently.</p>
        <a href="#signup" class="btn btn-light btn-lg">Start Your Free Trial</a>
        <p class="mt-3 mb-0">No credit card required • 14-day free trial • Cancel anytime</p>
    </div>
</section>

<!-- Footer -->
<footer id="contact">
    <div class="container">
        <div class="row">
            <div class="col-lg-4 mb-4">
                <div class="footer-brand">FlowSync</div>
                <p class="text-muted">The modern project management solution for teams that value efficiency and collaboration.</p>
                <div class="d-flex gap-3 mt-3">
                    <a href="#" class="text-muted"><i class="bi bi-twitter fs-5"></i></a>
                    <a href="#" class="text-muted"><i class="bi bi-linkedin fs-5"></i></a>
                    <a href="#" class="text-muted"><i class="bi bi-facebook fs-5"></i></a>
                    <a href="#" class="text-muted"><i class="bi bi-instagram fs-5"></i></a>
                </div>
            </div>
            <div class="col-lg-2 col-md-6 mb-4">
                <h6 class="mb-3">Product</h6>
                <div class="footer-links">
                    <a href="#features">Features</a>
                    <a href="#pricing">Pricing</a>
                    <a href="#integrations">Integrations</a>
                    <a href="#changelog">Changelog</a>
                </div>
            </div>
            <div class="col-lg-2 col-md-6 mb-4">
                <h6 class="mb-3">Company</h6>
                <div class="footer-links">
                    <a href="#about">About Us</a>
                    <a href="#careers">Careers</a>
                    <a href="#blog">Blog</a>
                    <a href="#press">Press</a>
                </div>
            </div>
            <div class="col-lg-2 col-md-6 mb-4">
                <h6 class="mb-3">Resources</h6>
                <div class="footer-links">
                    <a href="#help">Help Center</a>
                    <a href="#guides">Guides</a>
                    <a href="#webinars">Webinars</a>
                    <a href="#community">Community</a>
                </div>
            </div>
            <div class="col-lg-2 col-md-6 mb-4">
                <h6 class="mb-3">Legal</h6>
                <div class="footer-links">
                    <a href="#privacy">Privacy Policy</a>
                    <a href="#terms">Terms of Service</a>
                    <a href="#cookies">Cookie Policy</a>
                    <a href="#security">Security</a>
                </div>
            </div>
        </div>
        <div class="copyright text-center">
            <p>&copy; 2023 FlowSync. All rights reserved.</p>
        </div>
    </div>
</footer>

<!-- Bootstrap 5 JS Bundle with Popper -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

<!-- Minimal custom JavaScript -->
<script>
    // Smooth scrolling for anchor links
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            e.preventDefault();

            const targetId = this.getAttribute('href');
            if (targetId === '#') return;

            const targetElement = document.querySelector(targetId);
            if (targetElement) {
                const navbarHeight = document.querySelector('.navbar').offsetHeight;
                const targetPosition = targetElement.offsetTop - navbarHeight;

                window.scrollTo({
                    top: targetPosition,
                    behavior: 'smooth'
                });
            }
        });
    });

    // Add shadow to navbar on scroll
    window.addEventListener('scroll', function() {
        const navbar = document.querySelector('.navbar');
        if (window.scrollY > 50) {
            navbar.style.boxShadow = '0 4px 12px rgba(0, 0, 0, 0.1)';
        } else {
            navbar.style.boxShadow = '0 2px 10px rgba(0, 0, 0, 0.1)';
        }
    });

    // Simple form submission handler (for demonstration)
    document.querySelectorAll('a[href="#signup"]').forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            alert('Sign up form would appear here. This is a demonstration only.');
        });
    });
</script>
</body>
</html>

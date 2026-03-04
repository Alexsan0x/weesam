    <footer class="site-footer">
        <div class="footer-container">
            <div class="footer-top">
                <div class="footer-brand">
                    <a href="./" class="footer-logo">
                        <i class="fas fa-compass"></i>
                        <span>Dalili</span>
                    </a>
                    <p><?php echo t('Your intelligent guide to exploring Jordan\'s most beautiful destinations. Powered by AI and interactive maps.', 'دليلك الذكي لاستكشاف أجمل وجهات الأردن. مدعوم بالذكاء الاصطناعي والخرائط التفاعلية.'); ?></p>
                </div>

                <div class="footer-links">
                    <h4><?php echo t('Quick Links', 'روابط سريعة'); ?></h4>
                    <ul>
                        <li><a href="./"><?php echo t('Home', 'الرئيسية'); ?></a></li>
                        <li><a href="map.php"><?php echo t('Explore Map', 'استكشف الخريطة'); ?></a></li>
                        <li><a href="timeline.php"><?php echo t('Timeline', 'الخط الزمني'); ?></a></li>
                        <li><a href="about.php"><?php echo t('About Dalili', 'حول دليلي'); ?></a></li>
                    </ul>
                </div>

                <div class="footer-links">
                    <h4><?php echo t('Popular Destinations', 'وجهات شائعة'); ?></h4>
                    <ul>
                        <li><a href="map.php?place=petra"><?php echo t('Petra', 'البتراء'); ?></a></li>
                        <li><a href="map.php?place=wadi-rum"><?php echo t('Wadi Rum', 'وادي رم'); ?></a></li>
                        <li><a href="map.php?place=dead-sea"><?php echo t('Dead Sea', 'البحر الميت'); ?></a></li>
                        <li><a href="map.php?place=jerash"><?php echo t('Jerash', 'جرش'); ?></a></li>
                    </ul>
                </div>

                <div class="footer-links">
                    <h4><?php echo t('Contact', 'تواصل'); ?></h4>
                    <ul>
                        <li><i class="fas fa-university"></i> <?php echo t('Mutah University, Karak', 'جامعة مؤتة، الكرك'); ?></li>
                        <li><i class="fas fa-envelope"></i> dalili@mutah.edu.jo</li>
                        <li><i class="fas fa-map-marker-alt"></i> <?php echo t('Karak, Jordan', 'الكرك، الأردن'); ?></li>
                    </ul>
                </div>
            </div>

            <div class="footer-bottom">
                <p>&copy; <?php echo date('Y'); ?> Dalili - <?php echo t('Mutah University. All rights reserved.', 'جامعة مؤتة. جميع الحقوق محفوظة.'); ?></p>
                <p class="footer-credits"><?php echo t('Faculty of Information Technology - Graduation Project', 'كلية تكنولوجيا المعلومات - مشروع تخرج'); ?></p>
            </div>
        </div>
    </footer>

    <!-- Abu Mahmoud Floating Chat Widget -->
    <input type="hidden" id="siteLang" value="<?php echo $lang; ?>">
    <?php if (isLoggedIn()): ?>
    <div class="chat-fab" id="chatFab" title="<?php echo t('Chat with Abu Mahmoud', 'تحدث مع أبو محمود'); ?>">
        <i class="fas fa-comments"></i>
        <div class="chat-fab-pulse"></div>
    </div>

    <div class="chat-widget" id="chatWidget">
        <div class="chat-widget-header">
            <div class="chat-widget-header-left">
                <div class="chat-avatar-widget">
                    <i class="fas fa-user-tie"></i>
                    <span class="online-dot"></span>
                </div>
                <div>
                    <h4><?php echo t('Abu Mahmoud', 'أبو محمود'); ?></h4>
                    <p><?php echo t('AI Tourism Guide', 'مرشد سياحي ذكي'); ?></p>
                </div>
            </div>
            <div class="chat-widget-actions">
                <button class="chat-widget-btn" id="chatMinimize" title="<?php echo t('Minimize', 'تصغير'); ?>"><i class="fas fa-minus"></i></button>
                <button class="chat-widget-btn" id="chatClose" title="<?php echo t('Close', 'إغلاق'); ?>"><i class="fas fa-times"></i></button>
            </div>
        </div>
        <div class="chat-widget-messages" id="chatMessages">
            <div class="chat-message bot">
                <div class="bot-avatar"><i class="fas fa-user-tie"></i></div>
                <div class="msg-bubble">
                    <?php echo t(
                        'Ahlan wa sahlan! I\'m Abu Mahmoud, your virtual guide to Jordan. Ask me anything about tourist places, history, food, or travel tips! 🇯🇴',
                        'أهلاً وسهلاً! أنا أبو محمود، مرشدك الافتراضي في الأردن. اسألني عن أي مكان سياحي أو تاريخ أو طعام أو نصائح سفر! 🇯🇴'
                    ); ?>
                    <div class="msg-time"><?php echo t('Abu Mahmoud', 'أبو محمود'); ?></div>
                </div>
            </div>
        </div>
        <div class="typing-indicator" id="typingIndicator">
            <div class="bot-avatar"><i class="fas fa-user-tie"></i></div>
            <div class="typing-dots"><span></span><span></span><span></span></div>
        </div>
        <div class="chat-widget-suggestions" id="chatSuggestions">
            <button class="suggestion-chip" data-msg="<?php echo t('What are the best places to visit in Jordan?', 'ما هي أفضل الأماكن للزيارة في الأردن؟'); ?>"><?php echo t('Best places to visit', 'أفضل الأماكن'); ?></button>
            <button class="suggestion-chip" data-msg="<?php echo t('Tell me about Petra', 'أخبرني عن البتراء'); ?>"><?php echo t('About Petra', 'عن البتراء'); ?></button>
            <button class="suggestion-chip" data-msg="<?php echo t('Best food in Jordan?', 'أفضل الأكلات الأردنية؟'); ?>"><?php echo t('Local food', 'الأكل المحلي'); ?></button>
        </div>
        <div class="chat-widget-input">
            <input type="text" id="chatInput" placeholder="<?php echo t('Ask Abu Mahmoud anything...', 'اسأل أبو محمود أي شيء...'); ?>" autocomplete="off">
            <button class="chat-send-btn" id="chatSendBtn"><i class="fas fa-paper-plane"></i></button>
        </div>
    </div>
    <?php endif; ?>

    <script src="js/main.js"></script>
    <?php if (isLoggedIn()): ?>
    <script src="js/chat.js"></script>
    <?php endif; ?>
</body>
</html>

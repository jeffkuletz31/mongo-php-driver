/*
 * Copyright 2014-2017 MongoDB, Inc.
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *   http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

#ifdef HAVE_CONFIG_H
# include "config.h"
#endif

#include <php.h>
#include "phongo_compat.h"
#include "php_phongo.h"

ZEND_EXTERN_MODULE_GLOBALS(mongodb)

static char *php_phongo_make_subscriber_hash(zval *subscriber TSRMLS_DC)
{
	char *hash;
	int   hash_len;

	hash_len = spprintf(&hash, 0, "SUBS-%09d", Z_OBJ_HANDLE_P(subscriber));

	return hash;
}

/* {{{ proto void MongoDB\Monitoring::addSubscriber(MongoDB\Driver\Monitoring\Subscriber $subscriber)
   Adds a monitoring subscriber to the set of subscribers */
PHP_FUNCTION(MongoDB_Monitoring_addSubscriber)
{
	zval                         *zSubscriber = NULL;
	char                         *hash;
#if PHP_VERSION_ID >= 70000
	zval *subscriber;
#else
	zval **subscriber;
#endif

	SUPPRESS_UNUSED_WARNING(return_value_ptr) SUPPRESS_UNUSED_WARNING(return_value_used)

	if (zend_parse_parameters(ZEND_NUM_ARGS() TSRMLS_CC, "O", &zSubscriber, php_phongo_subscriber_ce) == FAILURE) {
		return;
	}

	hash = php_phongo_make_subscriber_hash(zSubscriber TSRMLS_CC);
	/* If we have already stored the subscriber, bail out. Otherwise, add
	 * subscriber to list */
#if PHP_VERSION_ID >= 70000
	if ((subscriber = zend_hash_str_find(&MONGODB_G(subscribers), hash, strlen(hash)))) {
		efree(hash);
		return;
	}

	zend_hash_str_update(&MONGODB_G(subscribers), hash, strlen(hash), zSubscriber);
#else
	if (zend_hash_find(&MONGODB_G(subscribers), hash, strlen(hash), (void**) &subscriber) == SUCCESS) {
		efree(hash);
		return;
	}

	zend_hash_update(&MONGODB_G(subscribers), hash, strlen(hash), (void*) &zSubscriber, sizeof(zval*), NULL);
#endif
	Z_ADDREF_P(zSubscriber);
	efree(hash);
}
/* }}} */

/* {{{ proto void MongoDB\Monitoring::removeSubscriber(MongoDB\Driver\Monitoring\Subscriber $subscriber)
   Removes a monitoring subscriber from the set of subscribers */
PHP_FUNCTION(MongoDB_Monitoring_removeSubscriber)
{
	zval                         *zSubscriber = NULL;
	char                         *hash;
	SUPPRESS_UNUSED_WARNING(return_value_ptr) SUPPRESS_UNUSED_WARNING(return_value_used)

	if (zend_parse_parameters(ZEND_NUM_ARGS() TSRMLS_CC, "O", &zSubscriber, php_phongo_subscriber_ce) == FAILURE) {
		return;
	}

	hash = php_phongo_make_subscriber_hash(zSubscriber TSRMLS_CC);

#if PHP_VERSION_ID >= 70000
	zend_hash_str_del(&MONGODB_G(subscribers), hash, strlen(hash));
#else
	zend_hash_del(&MONGODB_G(subscribers), hash, strlen(hash));
#endif
	efree(hash);
}
/* }}} */

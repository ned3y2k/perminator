<?php
namespace classes\web\script\handler;

interface ViewResolver {
	/**
	 * http://static.springsource.org/spring/docs/3.0.x/javadoc-api/org/springframework/web/servlet/ViewResolver.html
	 *
	 * Resolve the given view by name.
	 * Note: To allow for ViewResolver chaining, a ViewResolver should return null if a view with the given name is not defined in it. However, this is not required: Some ViewResolvers will always attempt to build View objects with the given name, unable to return null (rather throwing an exception when View creation failed).
	 *
	 * Parameters:
	 * viewName - name of the view to resolve
	 * locale - Locale in which to resolve the view. ViewResolvers that support internationalization should respect this.
	 * Returns:
	 * the View object, or null if not found (optional, to allow for ViewResolver chaining)
	 * Throws:
	 * Exception - if the view cannot be resolved (typically in case of problems creating an actual View object)
	 */
	function resolveViewName($viewName, \Locale $locale);
}